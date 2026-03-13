<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class StatsController extends Controller
{
    #[OA\Get(
        path: '/api/stats/itineraries-by-category',
        summary: 'Get itineraries count by category',
        tags: ['Stats'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Category counts',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/StatsItinerariesByCategoryRow')
                )
            )
        ]
    )]
    public function itinerariesByCategory()
    {
        $rows = Itinerary::query()
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        return response()->json($rows);
    }

    #[OA\Get(
        path: '/api/stats/users-by-month',
        summary: 'Get users registered per month',
        tags: ['Stats'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                in: 'query',
                required: false,
                description: 'Filter by year (YYYY)',
                schema: new OA\Schema(type: 'integer', example: 2026)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Monthly user counts',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/StatsUsersByMonthRow')
                )
            )
        ]
    )]
    public function usersByMonth(Request $request)
    {
        $year = $request->query('year');
        $year = $year !== null ? (int) $year : null;

        $connection = DB::connection();
        $driver = $connection->getDriverName();

        $monthExpression = match ($driver) {
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            'mysql', 'mariadb' => "DATE_FORMAT(created_at, '%Y-%m')",
            default => "strftime('%Y-%m', created_at)",
        };

        $query = User::query()
            ->selectRaw($monthExpression . ' as month, COUNT(*) as total')
            ->when($year !== null, function ($q) use ($year) {
                $q->whereBetween('created_at', ["{$year}-01-01 00:00:00", "{$year}-12-31 23:59:59"]);
            })
            ->groupBy('month')
            ->orderBy('month');

        return response()->json($query->get());
    }
}
