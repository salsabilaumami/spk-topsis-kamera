<?php

namespace App\Http\Controllers;

use App\Models\CalculationRun;
use App\Services\TopsisService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ProcessController extends Controller
{
    public function index(TopsisService $topsis): View
    {
        $latestRun = CalculationRun::query()->where('status', 'completed')->latest('id')->first();
        return view('process.index', [
            'readiness' => $topsis->readiness(),
            'latestRun' => $latestRun,
            'isStale' => $topsis->isStale($latestRun),
        ]);
    }

    public function store(Request $request, TopsisService $topsis): RedirectResponse
    {
        $validated = $request->validate(['name' => ['nullable', 'string', 'max:180']]);

        try {
            $run = $topsis->calculate($validated['name'] ?? null, $request->user()?->id);
            return redirect()->route('results.show', $run)->with('success', 'Perhitungan TOPSIS berhasil diselesaikan dan disimpan ke riwayat.');
        } catch (DomainException $exception) {
            return back()->with('error', $exception->getMessage())->withInput();
        } catch (Throwable $exception) {
            report($exception);
            return back()->with('error', 'Perhitungan gagal disimpan. Periksa koneksi database dan log aplikasi.')->withInput();
        }
    }
}
