<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    // Format JSON media
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
            // Munculkan data pivot hanya saat dipanggil dari relasi koleksi
            'pivot_data' => $this->whenPivotLoaded('media_user', function () {
                return [
                    'status' => $this->pivot->status,
                    'notes' => $this->pivot->notes,
                    'personal_rating' => $this->pivot->personal_rating,
                    'added_at' => $this->pivot->created_at?->toIso8601String(),
                ];
            }),
            // Munculkan daftar pengguna hanya saat eager-load ('users')
            'collectors' => UserSummaryResource::collection($this->whenLoaded('users')),
            'collectors_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
