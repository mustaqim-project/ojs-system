<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'journal_id',
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public static function get(string $key, mixed $default = null, ?int $journalId = null): mixed
    {
        try {
            $query = static::where('key', $key);

            if ($journalId) {
                $query->where('journal_id', $journalId);
            } else {
                $query->whereNull('journal_id');
            }

            $setting = $query->first();

            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'json'    => json_decode($setting->value, true),
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'integer' => (int) $setting->value,
                'float'   => (float) $setting->value,
                default   => $setting->value,
            };
        } catch (\Exception $e) {
            // If settings table doesn't exist yet (migrations not run), return default
            return $default;
        }
    }

    public static function set(string $key, mixed $value, ?int $journalId = null, string $type = 'string'): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key, 'journal_id' => $journalId],
            ['value' => $value, 'type' => $type]
        );

        return $setting;
    }
}
