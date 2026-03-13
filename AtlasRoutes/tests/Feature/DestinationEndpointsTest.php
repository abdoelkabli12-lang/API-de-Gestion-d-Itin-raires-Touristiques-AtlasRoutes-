<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestinationEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_add_destination_to_itinerary(): void
    {
        $user = User::factory()->create();
        $itinerary = $user->itineraries()->create([
            'title' => 'Atlas Adventure',
            'category' => 'mountain',
            'duration' => 3,
            'image' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson("/api/itineraries/{$itinerary->id}/destinations", [
            'name' => 'Ouarzazate',
            'accommodation' => 'Riad',
            'activities' => [
                ['name' => 'Ait Benhaddou', 'type' => 'place'],
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('destinations', ['name' => 'Ouarzazate', 'itinerary_id' => $itinerary->id]);
        $this->assertDatabaseHas('activities', ['name' => 'Ait Benhaddou', 'type' => 'place']);
    }

    public function test_non_owner_cannot_add_destination_to_itinerary(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $itinerary = $owner->itineraries()->create([
            'title' => 'Owner Adventure',
            'category' => 'mountain',
            'duration' => 3,
            'image' => null,
        ]);

        Sanctum::actingAs($other);

        $this->postJson("/api/itineraries/{$itinerary->id}/destinations", [
            'name' => 'Ifrane',
            'accommodation' => null,
            'activities' => [
                ['name' => 'Walk', 'type' => 'activity'],
            ],
        ])->assertForbidden();
    }
}

