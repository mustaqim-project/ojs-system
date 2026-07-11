<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'journal'        => [
                'id'           => $this->journal_id,
                'title'        => $this->journal->title ?? null,
                'abbreviation' => $this->journal->abbreviation ?? null,
            ],
            'issue'          => $this->issue ? [
                'id'     => $this->issue_id,
                'volume' => $this->issue->volume,
                'number' => $this->issue->number,
                'year'   => $this->issue->year,
                'title'  => $this->issue->title,
            ] : null,
            'title'          => $this->title,
            'slug'           => $this->slug,
            'abstract'       => $this->abstract,
            'keywords'       => $this->keywords_array,
            'language'       => $this->language,
            'doi'            => $this->doi,
            'pages'          => $this->pages_start && $this->pages_end ? "{$this->pages_start}-{$this->pages_end}" : null,
            'author'         => [
                'name'        => $this->author->name ?? null,
                'email'       => $this->author->email ?? null,
                'affiliation' => $this->author->affiliation ?? null,
                'orcid'       => $this->author->orcid ?? null,
            ],
            'urls' => [
                'public'     => route('public.articles.show', [$this->journal->slug ?? 'journal', $this->slug]),
                'manuscript' => $this->manuscript_file ? asset('storage/' . $this->manuscript_file) : null,
            ],
            'submitted_at'   => $this->submitted_at?->toIso8601String(),
            'accepted_at'    => $this->accepted_at?->toIso8601String(),
            'published_at'   => $this->published_at?->toIso8601String(),
            'status'         => $this->status,
            'status_label'   => $this->status_label,
        ];
    }
}
