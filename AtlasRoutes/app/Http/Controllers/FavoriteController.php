<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class FavoriteController extends Controller
{
    #[OA\Get(
        path: '/api/me/favorites',
        summary: 'List current user favorites',
        tags: ['Favorites'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Favorites list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Favorite')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function index(Request $request)
    {
        $favorites = $request->user()
            ->favorites()
            ->with('itinerary.destinations.activities')
            ->get();

        return response()->json($favorites);
    }

    #[OA\Post(
        path: '/api/itineraries/{itinerary}/favorite',
        summary: 'Add itinerary to favorites',
        tags: ['Favorites'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'itinerary',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Favorite created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Added to favorites.'),
                        new OA\Property(property: 'favorite', ref: '#/components/schemas/Favorite')
                    ]
                )
            ),
            new OA\Response(
                response: 200,
                description: 'Already in favorites',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Already in favorites.'),
                        new OA\Property(property: 'favorite', ref: '#/components/schemas/Favorite')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function store(Request $request, Itinerary $itinerary)
    {
        $favorite = $request->user()
            ->favorites()
            ->firstOrCreate(['itinerary_id' => $itinerary->id]);

        $status = $favorite->wasRecentlyCreated ? 201 : 200;

        return response()->json([
            'message' => $favorite->wasRecentlyCreated ? 'Added to favorites.' : 'Already in favorites.',
            'favorite' => $favorite,
        ], $status);
    }

    #[OA\Delete(
        path: '/api/itineraries/{itinerary}/favorite',
        summary: 'Remove itinerary from favorites',
        tags: ['Favorites'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'itinerary',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Favorite removed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Removed from favorites.')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function destroy(Request $request, Itinerary $itinerary)
    {
        $deleted = $request->user()
            ->favorites()
            ->where('itinerary_id', $itinerary->id)
            ->delete();

        return response()->json([
            'message' => $deleted ? 'Removed from favorites.' : 'Not in favorites.',
        ]);
    }
}
