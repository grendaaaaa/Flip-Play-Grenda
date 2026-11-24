<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'genre' => $this->genre,
            'release_year' => $this->release_year,
            'duration_minutes' => $this->duration_minutes,
            'rating' => (float) $this->rating,
            'description' => $this->description,
            // Custom collection metadata when loaded via pivot
            'pivot_data' => $this->whenPivotLoaded('media_user', function () {
                return [
                    'status' => $this->pivot->status,
                    'notes' => $this->pivot->notes,
                    'personal_rating' => $this->pivot->personal_rating,
                    'added_at' => $this->pivot->created_at?->toIso8601String(),
                ];
            }),
            // Included collectors when eager loaded
            'collectors' => UserSummaryResource::collection($this->whenLoaded('users')),
            'collectors_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
