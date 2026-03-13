<?php

namespace App\Policies;

use App\Models\Itinerary;
use App\Models\User;

class ItineraryPolicy
{
    public function update(User $user, Itinerary $itinerary): bool
    {
        return $user->id === $itinerary->user_id;
    }

    public function delete(User $user, Itinerary $itinerary): bool
    {
        return $user->id === $itinerary->user_id;
    }
}

