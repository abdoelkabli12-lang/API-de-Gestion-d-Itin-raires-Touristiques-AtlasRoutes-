<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OpenApi\Attributes as OA;

class DestinationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    #[OA\Get(
        path: '/api/itineraries/{itinerary}/destinations',
        summary: 'List destinations for an itinerary',
        tags: ['Destinations'],
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
                description: 'Destinations list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Destination')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function index(Itinerary $itinerary)
    {
        return response()->json(
            $itinerary->destinations()->with('activities')->get()
        );
    }

    #[OA\Post(
        path: '/api/itineraries/{itinerary}/destinations',
        summary: 'Add a destination to an itinerary',
        tags: ['Destinations'],
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
            content: new OA\JsonContent(ref: '#/components/schemas/DestinationCreateRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Destination created',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Destination added successfully'),
                        new OA\Property(property: 'destination', ref: '#/components/schemas/Destination')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(Request $request, Itinerary $itinerary)
    {
        $this->authorize('update', $itinerary);

        $request->validate([
            'name' => 'required|string|max:255',
            'accommodation' => 'nullable|string|max:255',
            'activities' => 'required|array|min:1',
            'activities.*.name' => 'required|string|max:255',
            'activities.*.type' => 'required|in:place,activity,dish',
        ]);

        $destination = $itinerary->destinations()->create([
            'name' => $request->name,
            'accommodation' => $request->accommodation,
        ]);

        foreach ($request->activities as $activityData) {
            $destination->activities()->create($activityData);
        }

        return response()->json([
            'message' => 'Destination added successfully',
            'destination' => $destination->load('activities'),
        ], 201);
    }

    #[OA\Get(
        path: '/api/destinations/{destination}',
        summary: 'Get a destination',
        tags: ['Destinations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'destination',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Destination details',
                content: new OA\JsonContent(ref: '#/components/schemas/Destination')
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function show(Destination $destination)
    {
        return response()->json($destination->load('activities'));
    }

    #[OA\Patch(
        path: '/api/destinations/{destination}',
        summary: 'Update a destination',
        tags: ['Destinations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'destination',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/DestinationUpdateRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Destination updated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Destination updated successfully'),
                        new OA\Property(property: 'destination', ref: '#/components/schemas/Destination')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function update(Request $request, Destination $destination)
    {
        $this->authorize('update', $destination);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'accommodation' => 'nullable|string|max:255',
            'activities' => 'sometimes|array|min:1',
            'activities.*.id' => 'sometimes|integer',
            'activities.*.name' => 'required_with:activities|string|max:255',
            'activities.*.type' => 'required_with:activities|in:place,activity,dish',
        ]);

        $destination->update($request->only(['name', 'accommodation']));

        if ($request->has('activities')) {
            foreach ($request->input('activities', []) as $activityData) {
                if (isset($activityData['id'])) {
                    $activity = $destination->activities()->whereKey($activityData['id'])->firstOrFail();
                    $activity->update(Arr::only($activityData, ['name', 'type']));
                    continue;
                }

                $destination->activities()->create(Arr::only($activityData, ['name', 'type']));
            }
        }

        return response()->json([
            'message' => 'Destination updated successfully',
            'destination' => $destination->load('activities'),
        ]);
    }

    #[OA\Delete(
        path: '/api/destinations/{destination}',
        summary: 'Delete a destination',
        tags: ['Destinations'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'destination',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Destination deleted',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Destination deleted successfully')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Forbidden'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function destroy(Request $request, Destination $destination)
    {
        $this->authorize('delete', $destination);

        $destination->delete();

        return response()->json([
            'message' => 'Destination deleted successfully'
        ]);
    }
}
