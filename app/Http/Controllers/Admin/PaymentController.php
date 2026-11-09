<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $status = $request->get('status', '');

        if ($status === 'pending') {
            $invoices = \App\Models\Invoice::where('status', 'waiting_payment')
                ->with(['submission.journal', 'submission.author'])
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $payments = $invoices->through(function ($invoice) {
                return (object)[
                    'id'           => null,
                    'invoice_code' => $invoice->invoice_number,
                    'status'       => 'pending',
                    'status_label' => 'Menunggu Pembayaran',
                    'amount'       => $invoice->amount - ($invoice->discount_amount ?? 0),
                    'created_at'   => $invoice->created_at,
                    'author'       => $invoice->submission?->author,
                    'article'      => $invoice->submission,
                ];
            });
        } else {
            $query = Payment::with(['invoice.submission.journal', 'author', 'verifiedBy'])->latest();

            if ($status) {
                $dbStatus = $status === 'uploaded' ? 'waiting_verification' : $status;
                $query->where('status', $dbStatus);
            }

            $payments = $query->paginate(15)->withQueryString();
        }

        $statuses = ['pending' => 'Menunggu', 'uploaded' => 'Diupload', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];

        return view('admin.payments.index', compact('payments', 'statuses', 'status'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice.submission.journal', 'author', 'verifiedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function proof(Payment $payment)
    {
        $proofPath = $payment->proof_path;
        if (!$proofPath) {
            abort(404, 'Bukti pembayaran belum diunggah.');
        }

        // 1. Cek disk private_upload
        if (\Illuminate\Support\Facades\Storage::disk('private_upload')->exists($proofPath)) {
            return \Illuminate\Support\Facades\Storage::disk('private_upload')->response($proofPath);
        }

        // 2. Cek disk public
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($proofPath)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->response($proofPath);
        }

        // 3. Cek path publik langsung
        if (file_exists(public_path($proofPath))) {
            return response()->file(public_path($proofPath));
        }

        abort(404, 'File bukti pembayaran tidak ditemukan di server.');
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if (!in_array($payment->status, ['waiting_verification', 'uploaded'])) {
            return back()->with('error', 'Pembayaran ini tidak bisa diverifikasi.');
        }

        $this->paymentService->verify($payment, auth()->id(), $request->admin_notes);

        // Notify Author safely
        try {
            $article = $payment->article ?? $payment->invoice?->submission;
            $author = $payment->author ?? $article?->author;
            if ($author && $article) {
                $author->notify(new \App\Notifications\PaymentVerifiedNotification($article));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed sending PaymentVerifiedNotification: ' . $e->getMessage());
        }

        $invoiceCode = $payment->invoice_code ?? $payment->invoice?->invoice_number ?? "#{$payment->id}";
        return redirect()->route('admin.payments.index')
            ->with('success', "Invoice {$invoiceCode} berhasil diverifikasi!");
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        if (!in_array($payment->status, ['waiting_verification', 'uploaded'])) {
            return back()->with('error', 'Pembayaran ini tidak bisa ditolak.');
        }

        $this->paymentService->reject($payment, auth()->id(), $request->admin_notes);

        // Notify Author that payment proof was rejected with notes
        try {
            $article = $payment->article ?? $payment->invoice?->submission;
            $author = $payment->author ?? $article?->author;
            if ($author && $article) {
                $author->notify(new \App\Notifications\PaymentRejectedNotification($article, $request->admin_notes));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed sending PaymentRejectedNotification: ' . $e->getMessage());
        }

        $invoiceCode = $payment->invoice_code ?? $payment->invoice?->invoice_number ?? "#{$payment->id}";
        return redirect()->route('admin.payments.index')
            ->with('success', "Bukti pembayaran untuk invoice {$invoiceCode} ditolak. Penulis telah diberitahu untuk mengunggah ulang.");
    }
}
