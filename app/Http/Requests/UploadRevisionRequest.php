<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadRevisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAuthor();
    }

    public function rules(): array
    {
        return [
            'revision_file' => [
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
            'revision_file.required' => 'File revisi wajib diunggah.',
            'revision_file.mimes'    => 'File revisi harus berformat PDF, DOC, atau DOCX.',
            'revision_file.max'      => 'Ukuran file revisi maksimal 10MB.',
        ];
    }
}
