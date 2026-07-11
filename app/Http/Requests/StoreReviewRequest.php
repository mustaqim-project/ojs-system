<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isReviewer();
    }

    public function rules(): array
    {
        return [
            'recommendation'     => ['required', 'in:accept,minor,major,reject'],
            'comments_to_author' => ['required', 'string', 'min:50', 'max:5000'],
            'comments_to_editor' => ['nullable', 'string', 'max:2000'],
            'review_file'        => [
                'nullable',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx',
            ],
            'originality_score'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'methodology_score'  => ['nullable', 'integer', 'min:1', 'max:5'],
            'relevance_score'    => ['nullable', 'integer', 'min:1', 'max:5'],
            'writing_score'      => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'recommendation.required'     => 'Rekomendasi wajib dipilih.',
            'recommendation.in'           => 'Rekomendasi tidak valid.',
            'comments_to_author.required' => 'Komentar untuk penulis wajib diisi.',
            'comments_to_author.min'      => 'Komentar minimal 50 karakter.',
        ];
    }
}
