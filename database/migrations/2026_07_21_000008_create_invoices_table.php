<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('submission_id')->constrained('articles')->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->dateTime('due_date');
            $table->enum('status', ['waiting_payment', 'waiting_verification', 'paid', 'waived', 'refunded', 'cancelled'])->default('waiting_payment');
            $table->boolean('waived')->default(false);
            $table->text('waiver_reason')->nullable();
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
