<?php

namespace Database\Factories;

use App\Models\Destination;
use App\Models\Itinerary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Destination>
 */
class DestinationFactory extends Factory
{
    protected $model = Destination::class;

    public function definition(): array
    {
        return [
            'itinerary_id' => Itinerary::factory(),
            'name' => fake()->city(),
            'accommodation' => fake()->boolean() ? fake()->word() : null,
        ];
    }
}

