<?php

namespace App\Http\Controllers;

use App\Models\CalculationRun;
use App\Services\TopsisService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function latest(): RedirectResponse
    {
        $run = CalculationRun::query()->where('status', 'completed')->latest('id')->first();
        return $run
            ? redirect()->route('results.show', $run)
            : redirect()->route('process.index')->with('error', 'Belum ada hasil perhitungan.');
    }

    public function show(CalculationRun $calculationRun, TopsisService $topsis): View
    {
        abort_unless($calculationRun->status === 'completed', 404);
        $calculationRun->load([
            'criteria',
            'alternatives' => fn ($query) => $query->orderBy('rank'),
            'alternatives.values',
        ]);

        $valueMap = [];
        foreach ($calculationRun->alternatives as $alternative) {
            foreach ($alternative->values as $value) {
                $valueMap[$alternative->id][$value->calculation_criterion_id] = $value;
            }
        }

        return view('results.show', [
            'run' => $calculationRun,
            'valueMap' => $valueMap,
            'isStale' => $topsis->isStale($calculationRun),
            'chartLabels' => $calculationRun->alternatives->pluck('code')->values()->all(),
            'chartValues' => $calculationRun->alternatives->pluck('preference')->map(fn ($value): float => (float) $value)->values()->all(),
        ]);
    }
}
