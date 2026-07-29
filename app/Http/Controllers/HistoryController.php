<?php

namespace App\Http\Controllers;

use App\Models\CalculationRun;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function index(): View
    {
        return view('history.index', [
            'runs' => CalculationRun::query()->where('status', 'completed')->latest('id')->paginate(12),
        ]);
    }

    public function destroy(CalculationRun $calculationRun): RedirectResponse
    {
        $calculationRun->delete();
        return redirect()->route('history.index')->with('success', 'Riwayat perhitungan berhasil dihapus.');
    }
}
