<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ReceiptService
{
    /**
     * Generate a receipt PDF for a paid invoice.
     */
    public function generateReceipt(Invoice $invoice): Receipt
    {
        $journal = $invoice->journal ?? $invoice->submission?->journal;
        $submission = $invoice->submission;
        $year = date('Y');
        $journalSlug = $journal?->slug ?? 'journal';

        $receiptNumber = sprintf(
            'RCP/%s/%s/%04d',
            $year,
            strtoupper($journalSlug),
            $invoice->id
        );

        // Generate PDF via dompdf
        $pdf = Pdf::loadView('pdfs.receipt', [
            'invoice'       => $invoice,
            'journal'       => $journal,
            'submission'    => $submission,
            'receiptNumber' => $receiptNumber,
        ]);

        $safeFileNumber = str_replace(['/', '\\'], '-', $receiptNumber);
        $path = "receipts/{$journalSlug}/{$year}/{$safeFileNumber}.pdf";
        Storage::disk('private_upload')->put($path, $pdf->output());

        $receipt = Receipt::updateOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'receipt_number' => $receiptNumber,
                'pdf_path'       => $path,
                'issued_at'      => now(),
            ]
        );

        try {
            AuditService::log('Finance', 'receipt_generated', $receipt, null, $receipt->toArray());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Audit log failed for receipt: ' . $e->getMessage());
        }

        return $receipt;
    }
}
