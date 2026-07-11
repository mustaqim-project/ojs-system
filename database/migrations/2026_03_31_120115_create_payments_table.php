<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel payments - sistem pembayaran manual APC (Article Processing Charge)
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('articles')->cascadeOnDelete();
            $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Invoice
            $table->string('invoice_code')->unique(); // Format: INV-{YEAR}-{ARTICLE_ID}-{RANDOM}
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('IDR');

            // Status pembayaran
            $table->enum('status', [
                'pending',      // Invoice dibuat, menunggu pembayaran
                'uploaded',     // Author sudah upload bukti
                'verified',     // Admin sudah verifikasi - PAID
                'rejected',     // Bukti pembayaran ditolak
            ])->default('pending');

            // File bukti pembayaran
            $table->string('proof_file')->nullable();
            $table->text('proof_notes')->nullable(); // Catatan dari author

            // Verifikasi admin
            $table->text('admin_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('uploaded_at')->nullable();

            // Informasi bank (dari settings saat invoice dibuat)
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->index('invoice_code');
            $table->index(['article_id', 'status']);
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
