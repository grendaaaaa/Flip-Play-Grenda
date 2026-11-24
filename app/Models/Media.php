<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    /**
     * Table associated with the model.
     */
    protected $table = 'media';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'duration_minutes',
        'rating',
        'description',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'release_year' => 'integer',
        'duration_minutes' => 'integer',
        'rating' => 'float',
    ];

    /**
     * Users who have added this media to their collection.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot('status', 'notes', 'personal_rating')
            ->withTimestamps();
    }
}
