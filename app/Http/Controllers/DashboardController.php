<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\CalculationRun;
use App\Models\Criterion;
use App\Services\TopsisService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(TopsisService $topsis): View
    {
        $latestRun = CalculationRun::query()
            ->where('status', 'completed')
            ->with('alternatives')
            ->latest('id')
            ->first();

        return view('dashboard', [
            'alternativeCount' => Alternative::count(),
            'criterionCount' => Criterion::count(),
            'totalWeight' => (float) Criterion::sum('weight'),
            'calculationCount' => CalculationRun::where('status', 'completed')->count(),
            'latestRun' => $latestRun,
            'isStale' => $topsis->isStale($latestRun),
            'readiness' => $topsis->readiness(),
            'chartLabels' => $latestRun?->alternatives->pluck('code')->values()->all() ?? [],
            'chartValues' => $latestRun?->alternatives->pluck('preference')->map(fn ($value): float => (float) $value)->values()->all() ?? [],
        ]);
    }
}
