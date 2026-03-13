<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\QueryBuilders\ItineraryQuery;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ItineraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'search', 'filter', 'popular']);
    }

    #[OA\Get(
        path: '/api/itineraries',
        summary: 'List itineraries',
        tags: ['Itineraries'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Itineraries list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Itinerary')
                )
            )
        ]
    )]
    public function index()
    {
        $itineraries = ItineraryQuery::base()->get();

        return response()->json($itineraries);
    }

    #[OA\Post(
        path: '/api/itineraries',
        summary: 'Create a new itinerary',
        tags: ['Itineraries'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ItineraryCreateRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Itinerary created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Itinerary created successfuly'),
                        new OA\Property(property: 'itinerary', ref: '#/components/schemas/Itinerary')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'duration' => 'required|integer|min:1',
            'image' => 'nullable|string',
            'destinations' => 'required|array|min:2',
            'destinations.*.name' => 'required|string|max:255',
            'destinations.*.accommodation' => 'nullable|string',
            'destinations.*.activities' => 'required|array|min:1',
            'destinations.*.activities.*.name' => 'required|string',
            'destinations.*.activities.*.type' => 'required|in:place,activity,dish',
        ]);

        $itinerary = $user->itineraries()->create(
            $request->only(['title', 'category', 'duration', 'image'])
        );

        foreach ($request->destinations as $destData) {
            $destination = $itinerary->destinations()->create([
                'name' => $destData['name'],
                'accommodation' => $destData['accommodation'] ?? null,
            ]);

            foreach ($destData['activities'] as $actData) {
                $destination->activities()->create($actData);
            }
        }

        return response()->json([
            'message' => 'Itinerary created successfuly',
            'itinerary' => $itinerary->load('destinations.activities')
        ], 201);
    }

    #[OA\Get(
        path: '/api/itineraries/{itinerary}',
        summary: 'Get itinerary details',
        tags: ['Itineraries'],
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
                description: 'Itinerary details',
                content: new OA\JsonContent(ref: '#/components/schemas/Itinerary')
            ),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function show(Itinerary $itinerary)
    {
        $itinerary = ItineraryQuery::base()->whereKey($itinerary->getKey())->firstOrFail();
        return response()->json($itinerary);
    }

    #[OA\Patch(
        path: '/api/itineraries/{itinerary}',
        summary: 'Update an itinerary',
        tags: ['Itineraries'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'itinerary',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ItineraryUpdateRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Itinerary updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Itinerary updated successfully'),
                        new OA\Property(property: 'itinerary', ref: '#/components/schemas/Itinerary')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function update(Request $request, Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:50',
            'duration' => 'sometimes|required|integer|min:1',
            'image' => 'nullable|string',
        ]);

        $itinerary->update($request->only(['title', 'category', 'duration', 'image']));

        return response()->json([
            'message' => 'Itinerary updated successfully',
            'itinerary' => $itinerary->load('destinations.activities')
        ]);
    }

    #[OA\Delete(
        path: '/api/itineraries/{itinerary}',
        summary: 'Delete an itinerary',
        tags: ['Itineraries'],
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
                description: 'Itinerary deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Itinerary deleted successfully')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function destroy(Request $request, Itinerary $itinerary)
    {
        $this->authorize('delete', $itinerary);
        $itinerary->delete();

        return response()->json([
            'message' => 'Itinerary deleted successfully'
        ]);
    }

    #[OA\Get(
        path: '/api/itineraries/search',
        summary: 'Search itineraries by title keyword',
        tags: ['Itineraries'],
        parameters: [
            new OA\Parameter(
                name: 'q',
                in: 'query',
                required: false,
                description: 'Search keyword',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Matching itineraries',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Itinerary')
                )
            )
        ]
    )]
    public function search(Request $request)
    {
        $keyword = $request->query('q', '');
        $itineraries = ItineraryQuery::searchTitle(ItineraryQuery::base(), $keyword)->get();

        return response()->json($itineraries);
    }

    #[OA\Get(
        path: '/api/itineraries/filter',
        summary: 'Filter itineraries by category and duration',
        tags: ['Itineraries'],
        parameters: [
            new OA\Parameter(
                name: 'category',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
            new OA\Parameter(
                name: 'duration',
                in: 'query',
                required: false,
                description: 'Max duration in days',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Filtered itineraries',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Itinerary')
                )
            )
        ]
    )]
    public function filter(Request $request)
    {
        $category = $request->query('category');
        $duration = $request->query('duration') !== null ? (int) $request->query('duration') : null;

        $query = ItineraryQuery::applyFilters(ItineraryQuery::base(), $category, $duration);

        return response()->json($query->get());
    }

    #[OA\Get(
        path: '/api/itineraries/popular',
        summary: 'Get most popular itineraries',
        tags: ['Itineraries'],
        parameters: [
            new OA\Parameter(
                name: 'limit',
                in: 'query',
                required: false,
                description: 'Max results (1-50)',
                schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 50)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Popular itineraries',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Itinerary')
                )
            )
        ]
    )]
    public function popular(Request $request)
    {
        $limit = $request->query('limit') !== null ? (int) $request->query('limit') : 10;
        $limit = max(1, min($limit, 50));

        $itineraries = ItineraryQuery::popular(ItineraryQuery::base())->limit($limit)->get();

        return response()->json($itineraries);
    }
}
