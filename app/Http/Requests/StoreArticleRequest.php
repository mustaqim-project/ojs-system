<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by controller/policy
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('keywords') && is_string($this->keywords)) {
            $keywords = array_values(array_filter(array_map('trim', explode(',', $this->keywords))));
            $this->merge([
                'keywords' => $keywords,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'journal_id'       => ['required', 'exists:journals,id'],
            'title'            => ['required', 'string', 'max:500'],
            'abstract'         => ['required', 'string', 'min:100', 'max:5000'],
            'keywords'         => ['required', 'array', 'min:2', 'max:10'],
            'keywords.*'       => ['string', 'max:50'],
            'language'         => ['required', 'in:en,id'],
            'manuscript_file'  => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'],
            'cover_letter'     => ['nullable', 'file', 'mimes:doc,docx,pdf', 'max:5120'],
            'author_note'      => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'journal_id.required'      => 'Pilih jurnal tujuan naskah.',
            'journal_id.exists'        => 'Jurnal yang dipilih tidak valid.',
            'title.required'           => 'Judul artikel wajib diisi.',
            'title.max'                => 'Judul artikel maksimal 500 karakter.',
            'abstract.required'        => 'Abstrak naskah wajib diisi.',
            'abstract.min'             => 'Abstrak minimal 100 karakter.',
            'abstract.max'             => 'Abstrak maksimal 5000 karakter.',
            'keywords.required'        => 'Kata kunci wajib diisi.',
            'keywords.array'           => 'Format kata kunci tidak valid.',
            'keywords.min'             => 'Sertakan minimal 2 kata kunci (pisahkan dengan koma).',
            'keywords.max'             => 'Maksimal 10 kata kunci.',
            'language.required'        => 'Pilih bahasa naskah.',
            'manuscript_file.required' => 'Berkas naskah utama (manuskrip) wajib diunggah.',
            'manuscript_file.mimes'    => 'Berkas manuskrip harus berformat PDF, DOC, atau DOCX.',
            'manuscript_file.max'      => 'Ukuran berkas manuskrip maksimal 10MB.',
            'cover_letter.mimes'       => 'Berkas Cover Letter harus berformat PDF, DOC, atau DOCX.',
            'cover_letter.max'         => 'Ukuran berkas Cover Letter maksimal 5MB.',
        ];
    }
}

