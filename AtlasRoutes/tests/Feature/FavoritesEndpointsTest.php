<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoritesEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_favorite_and_unfavorite_itinerary(): void
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();

        $itinerary = $owner->itineraries()->create([
            'title' => 'Shared Itinerary',
            'category' => 'nature',
            'duration' => 2,
            'image' => null,
        ]);

        Sanctum::actingAs($user);

        $this->postJson("/api/itineraries/{$itinerary->id}/favorite")
            ->assertStatus(201);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'itinerary_id' => $itinerary->id,
        ]);

        $this->getJson('/api/me/favorites')
            ->assertOk()
            ->assertJsonFragment(['itinerary_id' => $itinerary->id]);

        $this->deleteJson("/api/itineraries/{$itinerary->id}/favorite")
            ->assertOk();

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'itinerary_id' => $itinerary->id,
        ]);
    }
}

