<?php

namespace App;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Amina Zahra'),
        new OA\Property(property: 'email', type: 'string', example: 'amina@example.com'),
        new OA\Property(property: 'email_verified_at', type: 'string', nullable: true, example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-03-10T12:00:00Z'),
    ]
)]
#[OA\Schema(
    schema: 'Activity',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 10),
        new OA\Property(property: 'destination_id', type: 'integer', example: 4),
        new OA\Property(property: 'name', type: 'string', example: 'Visit the old medina'),
        new OA\Property(property: 'type', type: 'string', enum: ['place', 'activity', 'dish'], example: 'place'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-03-10T12:00:00Z'),
    ]
)]
#[OA\Schema(
    schema: 'Destination',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'itinerary_id', type: 'integer', example: 2),
        new OA\Property(property: 'name', type: 'string', example: 'Marrakesh'),
        new OA\Property(property: 'accommodation', type: 'string', nullable: true, example: 'Riad Atlas'),
        new OA\Property(
            property: 'activities',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Activity')
        ),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-03-10T12:00:00Z'),
    ]
)]
#[OA\Schema(
    schema: 'Itinerary',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 2),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Atlas Route Highlights'),
        new OA\Property(property: 'category', type: 'string', example: 'adventure'),
        new OA\Property(property: 'duration', type: 'integer', example: 5),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'https://example.com/atlas.jpg'),
        new OA\Property(property: 'favorites_count', type: 'integer', example: 12),
        new OA\Property(
            property: 'destinations',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Destination')
        ),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-03-10T12:00:00Z'),
    ]
)]
#[OA\Schema(
    schema: 'Favorite',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 7),
        new OA\Property(property: 'user_id', type: 'integer', example: 1),
        new OA\Property(property: 'itinerary_id', type: 'integer', example: 2),
        new OA\Property(property: 'itinerary', ref: '#/components/schemas/Itinerary'),
        new OA\Property(property: 'created_at', type: 'string', example: '2026-03-10T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', example: '2026-03-10T12:00:00Z'),
    ]
)]
#[OA\Schema(
    schema: 'StatsItinerariesByCategoryRow',
    type: 'object',
    properties: [
        new OA\Property(property: 'category', type: 'string', example: 'adventure'),
        new OA\Property(property: 'total', type: 'integer', example: 14),
    ]
)]
#[OA\Schema(
    schema: 'StatsUsersByMonthRow',
    type: 'object',
    properties: [
        new OA\Property(property: 'month', type: 'string', example: '2026-03'),
        new OA\Property(property: 'total', type: 'integer', example: 27),
    ]
)]
#[OA\Schema(
    schema: 'AuthRegisterRequest',
    type: 'object',
    required: ['name', 'email', 'password', 'password_confirmation'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Amina Zahra'),
        new OA\Property(property: 'email', type: 'string', example: 'amina@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password123'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'password123'),
    ]
)]
#[OA\Schema(
    schema: 'AuthLoginRequest',
    type: 'object',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', example: 'amina@example.com'),
        new OA\Property(property: 'password', type: 'string', example: 'password123'),
    ]
)]
#[OA\Schema(
    schema: 'ItineraryCreateRequest',
    type: 'object',
    required: ['title', 'category', 'duration', 'destinations'],
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Atlas Route Highlights'),
        new OA\Property(property: 'category', type: 'string', example: 'adventure'),
        new OA\Property(property: 'duration', type: 'integer', example: 5),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'https://example.com/atlas.jpg'),
        new OA\Property(
            property: 'destinations',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                required: ['name', 'activities'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Marrakesh'),
                    new OA\Property(property: 'accommodation', type: 'string', nullable: true, example: 'Riad Atlas'),
                    new OA\Property(
                        property: 'activities',
                        type: 'array',
                        items: new OA\Items(
                            type: 'object',
                            required: ['name', 'type'],
                            properties: [
                                new OA\Property(property: 'name', type: 'string', example: 'Visit the old medina'),
                                new OA\Property(property: 'type', type: 'string', enum: ['place', 'activity', 'dish'], example: 'place'),
                            ]
                        )
                    ),
                ]
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'ItineraryUpdateRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'title', type: 'string', example: 'Updated Atlas Route'),
        new OA\Property(property: 'category', type: 'string', example: 'culture'),
        new OA\Property(property: 'duration', type: 'integer', example: 7),
        new OA\Property(property: 'image', type: 'string', nullable: true, example: 'https://example.com/atlas.jpg'),
    ]
)]
#[OA\Schema(
    schema: 'DestinationCreateRequest',
    type: 'object',
    required: ['name', 'activities'],
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Chefchaouen'),
        new OA\Property(property: 'accommodation', type: 'string', nullable: true, example: 'Kasbah Blue'),
        new OA\Property(
            property: 'activities',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                required: ['name', 'type'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Hike to the waterfall'),
                    new OA\Property(property: 'type', type: 'string', enum: ['place', 'activity', 'dish'], example: 'activity'),
                ]
            )
        ),
    ]
)]
#[OA\Schema(
    schema: 'DestinationUpdateRequest',
    type: 'object',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Chefchaouen'),
        new OA\Property(property: 'accommodation', type: 'string', nullable: true, example: 'Kasbah Blue'),
        new OA\Property(
            property: 'activities',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                required: ['name', 'type'],
                properties: [
                    new OA\Property(property: 'id', type: 'integer', example: 10),
                    new OA\Property(property: 'name', type: 'string', example: 'Hike to the waterfall'),
                    new OA\Property(property: 'type', type: 'string', enum: ['place', 'activity', 'dish'], example: 'activity'),
                ]
            )
        ),
    ]
)]
final class OpenApiSchemas
{
}
