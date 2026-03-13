<?php

namespace App\Policies;

use App\Models\Destination;
use App\Models\User;

class DestinationPolicy
{
    public function update(User $user, Destination $destination): bool
    {
        $destination->loadMissing('itinerary');
        return $user->id === $destination->itinerary->user_id;
    }

    public function delete(User $user, Destination $destination): bool
    {
        $destination->loadMissing('itinerary');
        return $user->id === $destination->itinerary->user_id;
    }
}

