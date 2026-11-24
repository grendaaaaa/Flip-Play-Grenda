<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'collection_status' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->status),
            'personal_rating' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->personal_rating),
            'notes' => $this->whenPivotLoaded('media_user', fn () => $this->pivot->notes),
        ];
    }
}
