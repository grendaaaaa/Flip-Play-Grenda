<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Pengguna FlipPlay)
        $user1 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@flipplay.local',
            'password' => Hash::make('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Siti Rahma',
            'email' => 'siti@flipplay.local',
            'password' => Hash::make('password123'),
        ]);

        $user3 = User::create([
            'name' => 'Demo User',
            'email' => 'demo@flipplay.local',
            'password' => Hash::make('password123'),
        ]);

        // 2. Seed Media (Film, Anime, Serial, Podcast, Buku)
        $media1 = Media::create([
            'title' => 'Inception',
            'type' => 'Movie',
            'genre' => 'Sci-Fi / Action',
            'release_year' => 2010,
            'duration_minutes' => 148,
            'rating' => 8.8,
            'description' => 'A thief who steals corporate secrets through dream-sharing technology is given the inverse task of planting an idea.',
        ]);

        $media2 = Media::create([
            'title' => 'Interstellar',
            'type' => 'Movie',
            'genre' => 'Sci-Fi / Adventure',
            'release_year' => 2014,
            'duration_minutes' => 169,
            'rating' => 8.7,
            'description' => 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity survival.',
        ]);

        $media3 = Media::create([
            'title' => 'Breaking Bad',
            'type' => 'Series',
            'genre' => 'Crime / Drama',
            'release_year' => 2008,
            'duration_minutes' => 49,
            'rating' => 9.5,
            'description' => 'A chemistry teacher diagnosed with cancer turns to manufacturing methamphetamine to secure his family future.',
        ]);

        $media4 = Media::create([
            'title' => 'Attack on Titan',
            'type' => 'Anime',
            'genre' => 'Action / Dark Fantasy',
            'release_year' => 2013,
            'duration_minutes' => 24,
            'rating' => 9.1,
            'description' => 'After his hometown is destroyed, young Eren Jaeger vows to cleanse the earth of the giant Titans.',
        ]);

        $media5 = Media::create([
            'title' => 'The Joe Rogan Experience',
            'type' => 'Podcast',
            'genre' => 'Talk Show',
            'release_year' => 2009,
            'duration_minutes' => 180,
            'rating' => 8.0,
            'description' => 'Conversations with scientists, artists, and experts from all walks of life.',
        ]);

        // 3. Hubungkan beberapa media ke koleksi pengguna (Relasi Many-to-Many)
        // Budi mengoleksi Inception dan Attack on Titan
        $user1->media()->attach($media1->id, [
            'status' => 'completed',
            'notes' => 'Film mind-bending terbaik karya Nolan!',
            'personal_rating' => 10,
        ]);

        $user1->media()->attach($media4->id, [
            'status' => 'watching',
            'notes' => 'Sedang menonton final season part 2.',
            'personal_rating' => 9,
        ]);

        // Siti mengoleksi Interstellar dan Breaking Bad
        $user2->media()->attach($media2->id, [
            'status' => 'completed',
            'notes' => 'Soundtrack Hans Zimmer sangat emosional.',
            'personal_rating' => 9,
        ]);

        $user2->media()->attach($media3->id, [
            'status' => 'completed',
            'notes' => 'Masterpiece drama televisi!',
            'personal_rating' => 10,
        ]);
    }
}
