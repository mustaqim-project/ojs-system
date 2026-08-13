<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

/**
 * Model ApiIntegration
 *
 * Mengelola konfigurasi semua API eksternal yang tersimpan di database.
 * Field yang `is_secret = true` otomatis dienkripsi saat disimpan
 * dan didekripsi saat dibaca.
 *
 * @property string $provider
 * @property string $key
 * @property string|null $value
 * @property bool $is_secret
 * @property bool $is_required
 * @property string $field_type
 * @property array|null $field_options
 * @property string $status
 */
class ApiIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'key',
        'value',
        'label',
        'description',
        'is_secret',
        'is_required',
        'field_type',
        'field_options',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_secret'    => 'boolean',
        'is_required'  => 'boolean',
        'field_options' => 'array',
    ];

    // Cache TTL: 1 jam
    private const CACHE_TTL = 3600;

    // ─────────────────────────────────────────────────────────
    // ENKRIPSI OTOMATIS
    // ─────────────────────────────────────────────────────────

    /**
     * Override setAttribute: enkripsi otomatis untuk field secret.
     */
    public function setValueAttribute(?string $value): void
    {
        if ($this->is_secret && !empty($value)) {
            $this->attributes['value'] = Crypt::encryptString($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Override getValue: dekripsi otomatis untuk field secret.
     */
    public function getValueAttribute(?string $value): ?string
    {
        if ($this->is_secret && !empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                // Nilai mungkin belum terenkripsi (legacy data)
                Log::warning("ApiIntegration: gagal dekripsi {$this->provider}.{$this->key}");
                return $value;
            }
        }
        return $value;
    }

    // ─────────────────────────────────────────────────────────
    // STATIC HELPERS — Mirip Setting::get() tapi per-provider
    // ─────────────────────────────────────────────────────────

    /**
     * Ambil nilai satu key dalam provider.
     * Hasil di-cache selama 1 jam.
     *
     * Contoh: ApiIntegration::getValue('orcid', 'client_id')
     */
    public static function getValue(string $provider, string $key, mixed $default = null): mixed
    {
        $cacheKey = "api_integration_{$provider}_{$key}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provider, $key, $default) {
            $record = static::where('provider', $provider)
                ->where('key', $key)
                ->first();

            if (!$record) {
                return $default;
            }

            return $record->value ?? $default;
        });
    }

    /**
     * Ambil semua konfigurasi sebuah provider sebagai array asosiatif.
     * Nilai secret sudah terdekripsi.
     *
     * Contoh: ApiIntegration::getProvider('orcid')
     * → ['client_id' => '...', 'client_secret' => '...', 'mode' => 'public']
     */
    public static function getProvider(string $provider): array
    {
        $cacheKey = "api_provider_{$provider}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provider) {
            return static::where('provider', $provider)
                ->get()
                ->pluck('value', 'key')
                ->toArray();
        });
    }

    /**
     * Cek apakah sebuah provider sudah dikonfigurasi dan aktif.
     */
    public static function isEnabled(string $provider): bool
    {
        return Cache::remember("api_enabled_{$provider}", self::CACHE_TTL, function () use ($provider) {
            // Provider dianggap aktif jika ada minimal 1 record dengan status active
            return static::where('provider', $provider)
                ->where('status', 'active')
                ->exists();
        });
    }

    /**
     * Set nilai satu key dalam provider (upsert).
     * Enkripsi dilakukan otomatis jika is_secret = true.
     */
    public static function setValue(string $provider, string $key, mixed $value): void
    {
        $record = static::where('provider', $provider)
            ->where('key', $key)
            ->first();

        if ($record) {
            $record->update(['value' => $value]);
        }

        // Invalidate cache untuk provider ini
        static::clearCache($provider, $key);
    }

    /**
     * Aktifkan / nonaktifkan provider.
     */
    public static function setStatus(string $provider, string $status): void
    {
        static::where('provider', $provider)
            ->update(['status' => $status]);

        Cache::forget("api_enabled_{$provider}");
        Cache::forget("api_provider_{$provider}");
    }

    /**
     * Invalidate semua cache untuk provider atau key tertentu.
     */
    public static function clearCache(string $provider, ?string $key = null): void
    {
        if ($key) {
            Cache::forget("api_integration_{$provider}_{$key}");
        }
        Cache::forget("api_provider_{$provider}");
        Cache::forget("api_enabled_{$provider}");
    }

    // ─────────────────────────────────────────────────────────
    // SCOPES
    // ─────────────────────────────────────────────────────────

    public function scopeForProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('key');
    }

    // ─────────────────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────────────────

    /**
     * Nilai yang aman ditampilkan di UI (secret dimask).
     */
    public function getDisplayValueAttribute(): string
    {
        if ($this->is_secret && !empty($this->attributes['value'])) {
            return '••••••••';
        }
        return $this->value ?? '';
    }

    /**
     * Label status dengan warna untuk badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active'   => 'green',
            'testing'  => 'yellow',
            'inactive' => 'gray',
            default    => 'gray',
        };
    }
}
