<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAuthor();
    }

    public function rules(): array
    {
        return [
            'proof_file' => [
                'required',
                'file',
                'max:5120', // 5MB
                'mimes:jpg,jpeg,png,pdf',
            ],
            'proof_notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'proof_file.required' => 'Bukti pembayaran wajib diunggah.',
            'proof_file.mimes'    => 'Bukti pembayaran harus berformat JPG, PNG, atau PDF.',
            'proof_file.max'      => 'Ukuran file bukti pembayaran maksimal 5MB.',
        ];
    }
}
