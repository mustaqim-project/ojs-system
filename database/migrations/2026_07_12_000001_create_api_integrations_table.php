<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel api_integrations menyimpan semua credential API eksternal.
 *
 * KEAMANAN:
 * - Nilai yang `is_secret=true` disimpan terenkripsi (AES-256-CBC via Laravel encrypt())
 * - Admin panel hanya menampilkan placeholder "••••••••" untuk field secret
 * - Semua perubahan dicatat di audit log (via observer)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();

            // Provider: orcid, crossref, oai_pmh, datacite, doaj, pubmed, dll.
            $table->string('provider', 50)->index();

            // Key dalam provider: client_id, client_secret, mode, api_url, dll.
            $table->string('key', 100);

            // Nilai (dienkripsi jika is_secret = true)
            $table->text('value')->nullable();

            // Label untuk tampilan di admin panel
            $table->string('label', 150)->nullable();

            // Deskripsi / hint untuk field ini
            $table->text('description')->nullable();

            // Apakah nilai ini dienkripsi?
            $table->boolean('is_secret')->default(false);

            // Apakah field ini required?
            $table->boolean('is_required')->default(false);

            // Tipe field untuk UI: text, password, select, boolean, textarea, url
            $table->string('field_type', 20)->default('text');

            // Opsi untuk field select (JSON array)
            $table->json('field_options')->nullable();

            // Urutan tampil di admin panel
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Status integrasi: active, inactive, testing
            $table->enum('status', ['active', 'inactive', 'testing'])->default('inactive');

            $table->timestamps();

            // Setiap provider:key harus unik
            $table->unique(['provider', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_integrations');
    }
};
