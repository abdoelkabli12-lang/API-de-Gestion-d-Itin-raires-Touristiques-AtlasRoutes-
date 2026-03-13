<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItineraryEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_list_itineraries(): void
    {
        $user = User::factory()->create();
        $itinerary = $user->itineraries()->create([
            'title' => 'Atlas Adventure',
            'category' => 'mountain',
            'duration' => 3,
            'image' => null,
        ]);

        $destination = $itinerary->destinations()->create([
            'name' => 'Imlil',
            'accommodation' => 'Guesthouse',
        ]);

        $destination->activities()->create([
            'name' => 'Hike',
            'type' => 'activity',
        ]);

        $this->getJson('/api/itineraries')
            ->assertOk()
            ->assertJsonFragment(['title' => 'Atlas Adventure']);
    }

    public function test_authenticated_user_can_create_itinerary_with_nested_destinations_and_activities(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/itineraries', [
            'title' => 'Atlas adventure',
            'category' => 'nature',
            'duration' => 3,
            'image' => null,
            'destinations' => [
                [
                    'name' => 'Imlil',
                    'accommodation' => 'Guesthouse',
                    'activities' => [
                        ['name' => 'Hike', 'type' => 'activity'],
                    ],
                ],
                [
                    'name' => 'Ouzoud',
                    'accommodation' => null,
                    'activities' => [
                        ['name' => 'Waterfalls', 'type' => 'place'],
                    ],
                ],
            ],
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('itineraries', ['title' => 'Atlas adventure', 'user_id' => $user->id]);
        $this->assertDatabaseHas('destinations', ['name' => 'Imlil']);
        $this->assertDatabaseHas('activities', ['name' => 'Hike', 'type' => 'activity']);
    }

    public function test_user_cannot_update_another_users_itinerary(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $itinerary = $owner->itineraries()->create([
            'title' => 'Owner trip',
            'category' => 'beach',
            'duration' => 2,
            'image' => null,
        ]);

        Sanctum::actingAs($other);

        $this->putJson("/api/itineraries/{$itinerary->id}", [
            'title' => 'Hacked title',
        ])->assertForbidden();
    }
}

