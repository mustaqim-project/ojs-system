<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Generate invoice saat artikel ACCEPTED
     */
    public function generateInvoice(Article $article): Payment
    {
        // Cek apakah sudah ada invoice sebelumnya
        if ($article->payment) {
            return $article->payment;
        }

        $invoiceCode = $this->generateInvoiceCode($article->id);
        $amount      = (float) Setting::get('apc_amount', 500000);
        $currency    = Setting::get('apc_currency', 'IDR');

        return Payment::create([
            'article_id'   => $article->id,
            'author_id'    => $article->author_id,
            'invoice_code' => $invoiceCode,
            'amount'       => $amount,
            'currency'     => $currency,
            'status'       => 'pending',
            'bank_name'    => Setting::get('bank_name'),
            'bank_account' => Setting::get('bank_account'),
            'bank_holder'  => Setting::get('bank_holder'),
        ]);
    }

    /**
     * Author upload bukti pembayaran
     */
    public function uploadProof(Payment $payment, UploadedFile $file, ?string $notes = null): Payment
    {
        // Hapus file lama jika ada
        if ($payment->proof_file) {
            Storage::disk('public')->delete($payment->proof_file);
        }

        $filename  = 'payment-proof-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $proofPath = $file->storeAs('payments', $filename, 'public');

        $payment->update([
            'proof_file'  => $proofPath,
            'proof_notes' => $notes,
            'status'      => 'uploaded',
            'uploaded_at' => now(),
        ]);

        // Update status artikel
        $payment->article->update([
            'status' => Article::STATUS_PAYMENT_UPLOADED,
        ]);

        return $payment->fresh();
    }

    /**
     * Admin verifikasi pembayaran
     */
    public function verify(Payment $payment, int $adminId, ?string $adminNotes = null): Payment
    {
        return DB::transaction(function () use ($payment, $adminId, $adminNotes) {
            $payment->update([
                'status'      => 'verified',
                'verified_by' => $adminId,
                'admin_notes' => $adminNotes,
                'verified_at' => now(),
            ]);

            // Update status artikel ke PAID
            $payment->article->update([
                'status' => Article::STATUS_PAID,
            ]);

            return $payment->fresh();
        });
    }

    /**
     * Admin tolak bukti pembayaran
     */
    public function reject(Payment $payment, int $adminId, string $reason): Payment
    {
        return DB::transaction(function () use ($payment, $adminId, $reason) {
            $payment->update([
                'status'      => 'rejected',
                'verified_by' => $adminId,
                'admin_notes' => $reason,
            ]);

            // Kembalikan status artikel ke waiting payment
            $payment->article->update([
                'status' => Article::STATUS_WAITING_PAYMENT,
            ]);

            return $payment->fresh();
        });
    }

    /**
     * Generate kode invoice unik
     * Format: INV-{YEAR}-{ARTICLE_ID}-{RANDOM}
     */
    public function generateInvoiceCode(int $articleId): string
    {
        $year   = date('Y');
        $random = strtoupper(Str::random(6));
        $code   = "INV-{$year}-{$articleId}-{$random}";

        // Pastikan unik
        while (Payment::where('invoice_code', $code)->exists()) {
            $random = strtoupper(Str::random(6));
            $code   = "INV-{$year}-{$articleId}-{$random}";
        }

        return $code;
    }
}
