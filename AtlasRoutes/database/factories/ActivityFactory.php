<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Destination;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'destination_id' => Destination::factory(),
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['place', 'activity', 'dish']),
        ];
    }
}

