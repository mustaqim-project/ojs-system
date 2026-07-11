<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAuthor();
    }

    public function rules(): array
    {
        $maxSizeMb = (int) \App\Models\Setting::get('max_file_size_mb', 10);
        $maxSizeKb = $maxSizeMb * 1024;

        return [
            'journal_id'      => ['required', 'exists:journals,id'],
            'title'           => ['required', 'string', 'max:500'],
            'abstract'        => ['required', 'string', 'min:100', 'max:3000'],
            'keywords'        => ['required', 'string', 'max:255'],
            'language'        => ['required', 'in:id,en'],
            'manuscript_file' => [
                'required',
                'file',
                "max:{$maxSizeKb}",
                'mimes:pdf,doc,docx',
            ],
            'cover_letter' => [
                'nullable',
                'file',
                'max:5120',
                'mimes:pdf,doc,docx',
            ],
            'author_note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'journal_id.required'      => 'Jurnal wajib dipilih.',
            'journal_id.exists'        => 'Jurnal tidak ditemukan.',
            'title.required'           => 'Judul wajib diisi.',
            'abstract.required'        => 'Abstrak wajib diisi.',
            'abstract.min'             => 'Abstrak minimal 100 karakter.',
            'keywords.required'        => 'Kata kunci wajib diisi.',
            'manuscript_file.required' => 'File manuskrip wajib diunggah.',
            'manuscript_file.mimes'    => 'File manuskrip harus berformat PDF, DOC, atau DOCX.',
            'manuscript_file.max'      => 'Ukuran file manuskrip terlalu besar.',
        ];
    }
}
