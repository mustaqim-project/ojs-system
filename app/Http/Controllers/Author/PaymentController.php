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

        $payment = $article->payment;
        if (!$payment) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        return view('author.payments.show', compact('article', 'payment'));
    }

    public function uploadProof(UploadPaymentRequest $request, Article $article): RedirectResponse
    {
        if ($article->author_id !== auth()->id()) {
            abort(403);
        }

        $payment = $article->payment;
        if (!$payment) {
            abort(404);
        }

        // Hanya bisa upload jika status pending atau rejected
        if (!in_array($payment->status, ['pending', 'rejected'])) {
            return redirect()->route('author.payments.show', $article)
                ->with('error', 'Bukti pembayaran sudah diupload sebelumnya.');
        }

        $this->paymentService->uploadProof(
            $payment,
            $request->file('proof_file'),
            $request->proof_notes
        );

        return redirect()
            ->route('author.payments.show', $article)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Admin akan segera memverifikasi.');
    }
}
