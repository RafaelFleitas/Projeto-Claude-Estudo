<?php

namespace App\Http\Controllers;

use App\Enums\ContractStatus;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $baseQuery = $user->isAdmin()
            ? Contract::query()
            : Contract::where('user_id', $user->id);

        $total    = (clone $baseQuery)->count();
        $pending  = (clone $baseQuery)->where('status', ContractStatus::Pending)->count();
        $active   = (clone $baseQuery)->where('status', ContractStatus::Active)->count();
        $completed = (clone $baseQuery)->where('status', ContractStatus::Completed)->count();
        $cancelled = (clone $baseQuery)->where('status', ContractStatus::Cancelled)->count();

        $totalValue = (clone $baseQuery)->sum('valor_total');

        $recentContracts = (clone $baseQuery)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get(['id', 'contrato', 'projeto', 'status', 'valor_total', 'user_id', 'created_at']);

        $monthlyData = (clone $baseQuery)
            ->select(
                DB::raw("strftime('%Y-%m', created_at) as month"),
                DB::raw('count(*) as count'),
                DB::raw('sum(valor_total) as total')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('dashboard', [
            'stats' => [
                'total'     => $total,
                'pending'   => $pending,
                'active'    => $active,
                'completed' => $completed,
                'cancelled' => $cancelled,
                'totalValue' => number_format((float) $totalValue, 2, ',', '.'),
            ],
            'recentContracts' => $recentContracts,
            'monthlyData'     => $monthlyData,
        ]);
    }
}
