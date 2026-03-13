<?php

namespace App\QueryBuilders;

use App\Models\Itinerary;
use Illuminate\Database\Eloquent\Builder;

final class ItineraryQuery
{
    public static function base(): Builder
    {
        return Itinerary::query()
            ->with('destinations.activities')
            ->withCount('favorites');
    }

    public static function applyFilters(Builder $query, ?string $category, ?int $maxDuration): Builder
    {
        if ($category !== null && $category !== '') {
            $query->where('category', $category);
        }

        if ($maxDuration !== null) {
            $query->where('duration', '<=', $maxDuration);
        }

        return $query;
    }

    public static function searchTitle(Builder $query, ?string $keyword): Builder
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '') {
            return $query;
        }

        $keywordLower = mb_strtolower($keyword);

        return $query->whereRaw('LOWER(title) LIKE ?', ['%' . $keywordLower . '%']);
    }

    public static function popular(Builder $query): Builder
    {
        return $query->orderByDesc('favorites_count')->orderByDesc('id');
    }
}
