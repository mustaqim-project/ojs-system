<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\ReceiptService;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function invoices(Request $request)
    {
        $user = $request->user();

        $invoices = Invoice::query()
            ->when($user->hasRole('author'), fn($q) => $q->whereHas('submission', fn($q) => $q->where('author_id', $user->id)))
            ->with('submission', 'journal')
            ->paginate(20);

        return response()->json($invoices);
    }

    public function uploadPayment(Request $request, Invoice $invoice)
    {
        $user = $request->user();

        if ($invoice->submission->author_id !== $user->id) {
            abort(403, 'You can only upload payment for your own submissions.');
        }

        $validated = $request->validate([
            'proof_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (isset($validated['proof_file'])) {
            $validated['proof_path'] = $validated['proof_file']->store('payments', 'private_upload');
            unset($validated['proof_file']);
        }

        $validated['invoice_id'] = $invoice->id;
        $validated['author_id'] = $user->id;
        $validated['status'] = 'waiting_verification';

        $payment = Payment::create($validated);

        // Notify finance team
        $financeUsers = $invoice->journal->users()
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['finance', 'journal-manager']))
            ->get();

        foreach ($financeUsers as $finance) {
            $finance->notify(new \App\Notifications\PaymentUploadedNotification($invoice->submission));
        }

        return response()->json($payment, 201);
    }

    public function receipt(Request $request, Invoice $invoice)
    {
        if ($invoice->status !== 'paid') {
            abort(400, 'Invoice has not been paid yet.');
        }

        $receiptService = new ReceiptService();
        $receipt = $receiptService->generateReceipt($invoice);

        return response()->json($receipt);
    }
}
