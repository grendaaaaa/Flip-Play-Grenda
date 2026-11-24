<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlipPlayApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_list_media_with_filter(): void
    {
        $response = $this->getJson('/api/media?search=Inception');

        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Inception');
    }

    public function test_can_show_media_with_collectors(): void
    {
        $media = Media::where('title', 'Inception')->first();

        $response = $this->getJson("/api/media/{$media->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.title', 'Inception')
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'collectors',
                    'collectors_count',
                ],
            ]);
    }

    public function test_cannot_store_duplicate_media_title(): void
    {
        $response = $this->postJson('/api/media', [
            'title' => 'Inception',
            'type' => 'Movie',
            'genre' => 'Sci-Fi',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['title']]);
    }

    public function test_can_store_new_media(): void
    {
        $response = $this->postJson('/api/media', [
            'title' => 'The Matrix',
            'type' => 'Movie',
            'genre' => 'Cyberpunk / Action',
            'release_year' => 1999,
            'duration_minutes' => 136,
            'rating' => 8.7,
            'description' => 'A computer hacker learns from mysterious rebels about the true nature of his reality.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'The Matrix');

        $this->assertDatabaseHas('media', ['title' => 'The Matrix']);
    }

    public function test_can_update_media(): void
    {
        $media = Media::where('title', 'Inception')->first();

        $response = $this->putJson("/api/media/{$media->id}", [
            'genre' => 'Sci-Fi / Mind-Bender',
            'rating' => 9.2,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.genre', 'Sci-Fi / Mind-Bender')
            ->assertJsonPath('data.rating', 9.2);
    }

    public function test_can_delete_media(): void
    {
        $media = Media::where('title', 'The Joe Rogan Experience')->first();

        $response = $this->deleteJson("/api/media/{$media->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_can_add_media_to_user_collection(): void
    {
        $user = User::where('email', 'demo@flipplay.local')->first();
        $media = Media::where('title', 'Inception')->first();

        $response = $this->postJson("/api/users/{$user->id}/collection", [
            'media_id' => $media->id,
            'status' => 'plan_to_watch',
            'notes' => 'Rekomendasi dari teman',
            'personal_rating' => 9,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pivot_data.status', 'plan_to_watch');

        $this->assertDatabaseHas('media_user', [
            'user_id' => $user->id,
            'media_id' => $media->id,
            'status' => 'plan_to_watch',
        ]);
    }

    public function test_can_view_user_collection(): void
    {
        $user = User::where('email', 'budi@flipplay.local')->first();

        $response = $this->getJson("/api/users/{$user->id}/collection");

        $response->assertStatus(200)
            ->assertJsonPath('data.user.name', 'Budi Santoso')
            ->assertJsonCount(2, 'data.collection');
    }

    public function test_can_remove_media_from_user_collection(): void
    {
        $user = User::where('email', 'budi@flipplay.local')->first();
        $media = Media::where('title', 'Inception')->first();

        $response = $this->deleteJson("/api/users/{$user->id}/collection/{$media->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('media_user', [
            'user_id' => $user->id,
            'media_id' => $media->id,
        ]);
    }
}
