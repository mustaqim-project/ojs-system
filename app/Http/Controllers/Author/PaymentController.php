<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadPaymentRequest;
use App\Models\Article;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function show(Article $article): View
    {
        // Security: hanya pemilik artikel
        if ($article->author_id !== auth()->id()) {
            abort(403);
        }

        $invoice = $article->invoice;
        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $paymentRecord = $invoice->payments()->latest()->first();
        $bank = $article->journal->bankAccounts()->first() ?? (object)[
            'bank_name' => \App\Models\Setting::get('bank_name', 'Bank Transfer'),
            'bank_account' => \App\Models\Setting::get('bank_account', '-'),
            'bank_holder' => \App\Models\Setting::get('bank_holder', 'Journal Manager'),
        ];

        $status = 'pending';
        if ($paymentRecord) {
            if ($paymentRecord->status === 'waiting_verification') {
                $status = 'uploaded';
            } else {
                $status = $paymentRecord->status; // 'verified' or 'rejected'
            }
        }

        $statusLabels = [
            'pending'  => 'Menunggu Pembayaran',
            'uploaded' => 'Menunggu Verifikasi',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak',
        ];

        $payment = (object)[
            'invoice_code' => $invoice->invoice_number,
            'status'       => $status,
            'status_label' => $statusLabels[$status] ?? 'Menunggu',
            'amount'       => $invoice->amount - ($invoice->discount_amount ?? 0),
            'bank_name'    => $bank->bank_name ?? 'Bank Transfer',
            'bank_account' => $bank->bank_account ?? '-',
            'bank_holder'  => $bank->bank_holder ?? 'Journal Manager',
            'admin_notes'  => $paymentRecord ? $paymentRecord->notes : null,
            'verified_at'  => $paymentRecord ? $paymentRecord->verified_at : null,
        ];

        return view('author.payments.show', compact('article', 'payment'));
    }

    public function uploadProof(UploadPaymentRequest $request, Article $article): RedirectResponse
    {
        if ($article->author_id !== auth()->id()) {
            abort(403);
        }

        $invoice = $article->invoice;
        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $paymentRecord = $invoice->payments()->latest()->first();
        $status = 'pending';
        if ($paymentRecord) {
            if ($paymentRecord->status === 'waiting_verification') {
                $status = 'uploaded';
            } else {
                $status = $paymentRecord->status;
            }
        }

        // Hanya bisa upload jika status pending atau rejected
        if (!in_array($status, ['pending', 'rejected'])) {
            return redirect()->route('author.payments.show', $article)
                ->with('error', 'Bukti pembayaran sudah diupload sebelumnya.');
        }

        $this->paymentService->uploadProof(
            $invoice,
            $request->file('proof_file'),
            $request->proof_notes
        );

        // Notify Admins
        $admins = \App\Models\User::where('role', 'admin')->active()->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\PaymentUploadedNotification($article));

        return redirect()
            ->route('author.payments.show', $article)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Admin akan segera memverifikasi.');
    }
}
