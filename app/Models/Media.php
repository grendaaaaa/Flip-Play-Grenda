<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    // Kolom yang boleh diisi massal
    protected $fillable = [
        'title',
        'type',
        'genre',
        'release_year',
        'duration_minutes',
        'rating',
        'description',
    ];

    // Konversi tipe data otomatis
    protected $casts = [
        'release_year' => 'integer',
        'duration_minutes' => 'integer',
        'rating' => 'float',
    ];

    // Relasi Many-to-Many: Media dikoleksi oleh banyak pengguna
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'media_user')
            ->withPivot('status', 'notes', 'personal_rating')
            ->withTimestamps();
    }
}
