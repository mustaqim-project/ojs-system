<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Invoice;
use App\Models\Journal;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceService
{
    /**
     * Generate invoice for an article when it passes screening/editorial decision.
     */
    public function generateInvoice(Article $article): Invoice
    {
        return DB::transaction(function () use ($article) {
            $journal = $article->journal;
            $amount = $journal->apc_default_amount
                ?? Setting::get('apc_amount', 500000);
            $currency = $journal->apc_currency
                ?? Setting::get('apc_currency', 'IDR');

            $invoice = Invoice::create([
                'uuid'           => Str::uuid()->toString(),
                'journal_id'     => $article->journal_id,
                'submission_id'  => $article->id,
                'invoice_number' => $this->generateInvoiceNumber($article->journal_id),
                'amount'         => $amount,
                'currency'       => $currency,
                'due_date'       => now()->addDays((int) Setting::get('invoice_due_days', 14)),
                'status'         => 'waiting_payment',
            ]);

            // Audit trail
            AuditService::log('Finance', 'invoice_generated', $invoice, null, $invoice->toArray());

            return $invoice;
        });
    }

    /**
     * Apply a full waiver to an invoice.
     */
    public function applyWaiver(Invoice $invoice, string $reason, ?int $approverId = null): Invoice
    {
        return DB::transaction(function () use ($invoice, $reason, $approverId) {
            $old = $invoice->toArray();

            $invoice->update([
                'waived'       => true,
                'waiver_reason' => $reason,
                'approved_by'  => $approverId,
                'status'       => 'waived',
            ]);

            // Unblock submission — move to reviewer assignment
            if ($invoice->submission) {
                $invoice->submission->update(['current_stage' => 'reviewer_assignment']);
            }

            AuditService::log('Finance', 'waiver_applied', $invoice, $old, $invoice->toArray());

            return $invoice->fresh();
        });
    }

    /**
     * Apply a partial discount to an invoice.
     */
    public function applyDiscount(Invoice $invoice, float $discountAmount, string $reason, ?int $approverId = null): Invoice
    {
        return DB::transaction(function () use ($invoice, $discountAmount, $reason, $approverId) {
            $old = $invoice->toArray();

            $invoice->update([
                'discount_amount' => $discountAmount,
                'waiver_reason'   => $reason,
                'approved_by'     => $approverId,
            ]);

            AuditService::log('Finance', 'discount_applied', $invoice, $old, $invoice->toArray());

            return $invoice->fresh();
        });
    }

    /**
     * Generate a unique invoice number with DB-level race condition protection.
     */
    private function generateInvoiceNumber(int $journalId): string
    {
        $journal = Journal::find($journalId);
        $code = $journal ? strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $journal->slug), 0, 3)) : 'JRN';
        $year = date('Y');

        return DB::transaction(function () use ($code, $year) {
            $last = Invoice::where('invoice_number', 'like', "INV/{$code}/{$year}/%")
                ->lockForUpdate()
                ->count();

            return sprintf('INV/%s/%s/%04d', $code, $year, $last + 1);
        });
    }
}
