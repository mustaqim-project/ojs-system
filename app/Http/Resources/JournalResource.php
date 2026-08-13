<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'abbreviation' => $this->abbreviation,
            'slug'         => $this->slug,
            'description'  => $this->description,
            'issn_p'       => $this->issn_print,
            'issn_e'       => $this->issn_online,
            'is_active'    => (bool) $this->is_active,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            'links'        => [
                'self'   => route('public.journals.show', $this->slug),
                'api'    => route('api.v1.journals.show', $this->id),
            ]
        ];
    }
}
