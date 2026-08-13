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
        $journal = $invoice->journal;
        $submission = $invoice->submission;
        $year = date('Y');

        $receiptNumber = sprintf(
            'RCP/%s/%s/%04d',
            $year,
            $journal->slug,
            $invoice->id
        );

        // Generate PDF via dompdf
        $pdf = Pdf::loadView('pdfs.receipt', [
            'invoice'       => $invoice,
            'journal'       => $journal,
            'submission'    => $submission,
            'receiptNumber' => $receiptNumber,
        ]);

        $path = "receipts/{$journal->slug}/{$year}/{$receiptNumber}.pdf";
        Storage::disk('private_upload')->put($path, $pdf->output());

        $receipt = Receipt::create([
            'invoice_id'     => $invoice->id,
            'receipt_number' => $receiptNumber,
            'pdf_path'       => $path,
            'issued_at'      => now(),
        ]);

        AuditService::log('Finance', 'receipt_generated', $receipt, null, $receipt->toArray());

        return $receipt;
    }
}
