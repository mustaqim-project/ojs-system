<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by controller/policy
    }

    public function rules(): array
    {
        return [
            'journal_id'       => ['required', 'exists:journals,id'],
            'title'            => ['required', 'string', 'max:500'],
            'abstract'         => ['required', 'string', 'min:150', 'max:5000'],
            'keywords'         => ['required', 'array', 'min:3', 'max:10'],
            'keywords.*'       => ['string', 'max:50'],
            'language'         => ['required', 'in:en,id'],
            'section'          => ['nullable', 'string', 'max:100'],
            'manuscript_file'  => ['required', 'file', 'mimes:doc,docx,pdf', 'max:10240'],
            'cover_letter'     => ['nullable', 'file', 'mimes:doc,docx,pdf', 'max:5120'],
            'author_note'      => ['nullable', 'string', 'max:2000'],
            'funding_statement' => ['nullable', 'string', 'max:1000'],
            'conflict_of_interest' => ['required', 'string', 'max:1000'],
            'ethics_statement' => ['nullable', 'string', 'max:1000'],
            'acknowledgement'  => ['nullable', 'string', 'max:1000'],
            'license'          => ['required', 'in:cc-by,cc-by-nc,cc-by-nc-nd,cc-by-sa,all-rights-reserved'],
        ];
    }

    public function messages(): array
    {
        return [
            'abstract.min' => 'Abstract must be at least 150 characters.',
            'keywords.min' => 'Please provide at least 3 keywords.',
            'keywords.max' => 'Please provide no more than 10 keywords.',
            'manuscript_file.required' => 'Manuscript file is required.',
            'manuscript_file.mimes' => 'Manuscript must be a PDF, DOC, or DOCX file.',
            'manuscript_file.max' => 'Manuscript file must not exceed 10MB.',
            'conflict_of_interest.required' => 'Conflict of interest statement is required.',
        ];
    }
}
