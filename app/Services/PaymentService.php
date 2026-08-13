<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Author uploads proof of payment for an Invoice.
     */
    public function uploadProof(Invoice $invoice, UploadedFile $file, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($invoice, $file, $notes) {
            // Delete old payment proof if it exists
            $existingPayment = Payment::where('invoice_id', $invoice->id)->first();
            if ($existingPayment) {
                if ($existingPayment->proof_path) {
                    Storage::disk('private_upload')->delete($existingPayment->proof_path);
                }
                $existingPayment->delete();
            }

            $filename = 'proof-' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('payments', $filename, 'private_upload');

            $payment = Payment::create([
                'invoice_id'     => $invoice->id,
                'author_id'      => $invoice->submission->author_id,
                'amount'         => $invoice->amount - ($invoice->discount_amount ?? 0),
                'payment_method' => 'bank_transfer',
                'payment_date'   => now(),
                'proof_path'     => $path,
                'status'         => 'waiting_verification',
                'notes'          => $notes,
            ]);

            $invoice->update(['status' => 'waiting_verification']);
            $invoice->submission->update(['status' => 'payment_uploaded']);

            return $payment;
        });
    }

    /**
     * Admin verifies a payment.
     */
    public function verify(Payment $payment, int $adminId, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($payment, $adminId, $notes) {
            $payment->update([
                'status'      => 'verified',
                'verified_by' => $adminId,
                'verified_at' => now(),
                'notes'       => $notes,
            ]);

            $invoice = $payment->invoice;
            $invoice->update([
                'status'      => 'paid',
                'approved_by' => $adminId,
            ]);

            $invoice->submission->update(['status' => 'paid']);

            // Auto-generate receipt
            $receiptService = new ReceiptService();
            $receiptService->generateReceipt($invoice);

            return $payment->fresh();
        });
    }

    /**
     * Admin rejects a payment.
     */
    public function reject(Payment $payment, int $adminId, string $reason): Payment
    {
        return DB::transaction(function () use ($payment, $adminId, $reason) {
            $payment->update([
                'status'      => 'rejected',
                'verified_by' => $adminId,
                'notes'       => $reason,
            ]);

            $invoice = $payment->invoice;
            $invoice->update(['status' => 'waiting_payment']);
            $invoice->submission->update(['status' => 'waiting_payment']);

            return $payment->fresh();
        });
    }
}
