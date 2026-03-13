<?php


use App\Http\Controllers\AuthController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StatsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItineraryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('itineraries/search', [ItineraryController::class, 'search']);
Route::get('itineraries/filter', [ItineraryController::class, 'filter']);
Route::get('itineraries/popular', [ItineraryController::class, 'popular']);

Route::get('stats/itineraries-by-category', [StatsController::class, 'itinerariesByCategory']);
Route::get('stats/users-by-month', [StatsController::class, 'usersByMonth']);

Route::apiResource('itineraries', ItineraryController::class);

Route::apiResource('itineraries.destinations', DestinationController::class)->shallow();

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me/favorites', [FavoriteController::class, 'index']);
    Route::post('/itineraries/{itinerary}/favorite', [FavoriteController::class, 'store']);
    Route::delete('/itineraries/{itinerary}/favorite', [FavoriteController::class, 'destroy']);
});
