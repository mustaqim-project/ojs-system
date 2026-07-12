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
        $query  = Payment::with(['article.journal', 'author', 'verifiedBy'])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        $payments = $query->paginate(15)->withQueryString();
        $statuses = ['pending' => 'Menunggu', 'uploaded' => 'Diupload', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak'];

        return view('admin.payments.index', compact('payments', 'statuses', 'status'));
    }

    public function show(Payment $payment): View
    {
        $payment->load(['article.journal', 'article.author', 'author', 'verifiedBy']);
        return view('admin.payments.show', compact('payment'));
    }

    public function verify(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        if ($payment->status !== 'uploaded') {
            return back()->with('error', 'Pembayaran ini tidak bisa diverifikasi.');
        }

        $this->paymentService->verify($payment, auth()->id(), $request->admin_notes);

        // Notify Author
        $payment->author->notify(new \App\Notifications\PaymentVerifiedNotification($payment->article));

        return redirect()->route('admin.payments.index')
            ->with('success', "Invoice {$payment->invoice_code} berhasil diverifikasi!");
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $request->validate([
            'admin_notes' => ['required', 'string', 'max:500'],
        ]);

        if ($payment->status !== 'uploaded') {
            return back()->with('error', 'Pembayaran ini tidak bisa ditolak.');
        }

        $this->paymentService->reject($payment, auth()->id(), $request->admin_notes);

        return back()->with('success', 'Bukti pembayaran ditolak. Author akan diminta mengupload ulang.');
    }
}
