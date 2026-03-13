<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'AtlasRoutes API',
    description: 'API for managing tourist itineraries, destinations, and user favorites.'
)]
#[OA\Server(url: '/')]
#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
#[OA\Tag(name: 'Itineraries', description: 'Browse and manage itineraries')]
#[OA\Tag(name: 'Destinations', description: 'Manage itinerary destinations and activities')]
#[OA\Tag(name: 'Favorites', description: 'User favorites management')]
#[OA\Tag(name: 'Stats', description: 'Reporting and statistics')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'Token'
)]
final class OpenApi
{
}
