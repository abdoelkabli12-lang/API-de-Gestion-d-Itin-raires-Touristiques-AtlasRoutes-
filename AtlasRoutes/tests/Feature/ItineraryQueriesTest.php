<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Itinerary;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryQueriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_itineraries_by_title_keyword(): void
    {
        $user = User::factory()->create();

        Itinerary::factory()->for($user)->create(['title' => 'Atlas Adventure']);
        Itinerary::factory()->for($user)->create(['title' => 'Beach Escape']);

        $response = $this->getJson('/api/itineraries/search?q=atlas');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['title' => 'Atlas Adventure']);
    }

    public function test_filter_itineraries_by_category_and_duration(): void
    {
        $user = User::factory()->create();

        Itinerary::factory()->for($user)->create(['category' => 'mountain', 'duration' => 3]);
        Itinerary::factory()->for($user)->create(['category' => 'mountain', 'duration' => 5]);
        Itinerary::factory()->for($user)->create(['category' => 'beach', 'duration' => 2]);

        $response = $this->getJson('/api/itineraries/filter?category=mountain&duration=3');

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonFragment(['category' => 'mountain', 'duration' => 3]);
    }

    public function test_popular_itineraries_are_ordered_by_favorites_count(): void
    {
        $owner = User::factory()->create();
        $u1 = User::factory()->create();
        $u2 = User::factory()->create();

        $itineraryA = Itinerary::factory()->for($owner)->create();
        $itineraryB = Itinerary::factory()->for($owner)->create();

        Favorite::factory()->create(['user_id' => $u1->id, 'itinerary_id' => $itineraryA->id]);
        Favorite::factory()->create(['user_id' => $u2->id, 'itinerary_id' => $itineraryA->id]);
        Favorite::factory()->create(['user_id' => $u1->id, 'itinerary_id' => $itineraryB->id]);

        $response = $this->getJson('/api/itineraries/popular?limit=2');

        $response->assertOk();
        $response->assertJsonCount(2);
        $response->assertJsonPath('0.id', $itineraryA->id);
        $response->assertJsonPath('0.favorites_count', 2);
    }
}

