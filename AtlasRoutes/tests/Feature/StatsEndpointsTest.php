<?php

namespace Tests\Feature;

use App\Models\Itinerary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsEndpointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_itineraries_by_category_stats(): void
    {
        $user = User::factory()->create();

        Itinerary::factory()->for($user)->count(2)->create(['category' => 'mountain']);
        Itinerary::factory()->for($user)->create(['category' => 'beach']);

        $data = $this->getJson('/api/stats/itineraries-by-category')
            ->assertOk()
            ->json();

        $this->assertTrue(collect($data)->contains(function ($row) {
            return $row['category'] === 'mountain' && (int) $row['total'] === 2;
        }));
        $this->assertTrue(collect($data)->contains(function ($row) {
            return $row['category'] === 'beach' && (int) $row['total'] === 1;
        }));
    }

    public function test_users_by_month_stats_for_specific_year(): void
    {
        $u1 = User::factory()->create();
        $u1->timestamps = false;
        $u1->forceFill(['created_at' => Carbon::parse('2026-01-15 10:00:00'), 'updated_at' => Carbon::parse('2026-01-15 10:00:00')])->save();

        $u2 = User::factory()->create();
        $u2->timestamps = false;
        $u2->forceFill(['created_at' => Carbon::parse('2026-01-20 10:00:00'), 'updated_at' => Carbon::parse('2026-01-20 10:00:00')])->save();

        $u3 = User::factory()->create();
        $u3->timestamps = false;
        $u3->forceFill(['created_at' => Carbon::parse('2026-02-02 10:00:00'), 'updated_at' => Carbon::parse('2026-02-02 10:00:00')])->save();

        $data = $this->getJson('/api/stats/users-by-month?year=2026')
            ->assertOk()
            ->json();

        $this->assertTrue(collect($data)->contains(function ($row) {
            return $row['month'] === '2026-01' && (int) $row['total'] === 2;
        }));
        $this->assertTrue(collect($data)->contains(function ($row) {
            return $row['month'] === '2026-02' && (int) $row['total'] === 1;
        }));
    }
}

