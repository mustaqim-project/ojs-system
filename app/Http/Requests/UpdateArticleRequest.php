<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAuthor();
    }

    public function rules(): array
    {
        return [
            'article_file' => [
                'required',
                'file',
                'max:10240',
                'mimes:pdf,doc,docx',
            ],
            'author_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'article_file.required' => 'File Article wajib diunggah.',
            'article_file.mimes'    => 'File Article harus berformat PDF, DOC, atau DOCX.',
            'article_file.max'      => 'Ukuran file Article maksimal 10MB.',
        ];
    }
}
