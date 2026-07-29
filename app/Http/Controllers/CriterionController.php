<?php

namespace App\Http\Controllers;

use App\Models\Criterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CriterionController extends Controller
{
    public function index(): View
    {
        return view('criteria.index', [
            'criteria' => Criterion::query()->withCount('assessments')->orderBy('id')->get(),
            'totalWeight' => (float) Criterion::sum('weight'),
        ]);
    }

    public function create(): View
    {
        return view('criteria.form', [
            'criterion' => new Criterion(),
            'totalWeight' => (float) Criterion::sum('weight'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Criterion::create($this->validated($request));

        return redirect()
            ->route('kriteria.index')
            ->with('success', 'Kriteria berhasil ditambahkan. Bobot akan dinormalisasi otomatis saat TOPSIS dihitung.');
    }

    public function edit(Criterion $criterion): View
    {
        return view('criteria.form', [
            'criterion' => $criterion,
            'totalWeight' => (float) Criterion::sum('weight'),
        ]);
    }

    public function update(Request $request, Criterion $criterion): RedirectResponse
    {
        $criterion->update($this->validated($request));

        return redirect()
            ->route('kriteria.index')
            ->with('success', 'Kriteria berhasil diperbarui. Bobot akan dinormalisasi otomatis saat TOPSIS dihitung.');
    }

    public function destroy(Criterion $criterion): RedirectResponse
    {
        $assessmentCount = $criterion->assessments()->count();
        $criterion->delete();

        return redirect()->route('kriteria.index')->with(
            'success',
            $assessmentCount > 0
                ? "Kriteria dihapus bersama {$assessmentCount} nilai terkait. Riwayat perhitungan tetap tersimpan."
                : 'Kriteria berhasil dihapus.'
        );
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'unit' => ['nullable', 'string', 'max:100'],
            'type' => ['required', Rule::in(['benefit', 'cost'])],
            'weight' => ['required', 'numeric', 'gt:0', 'lte:99999.99999'],
            'description' => ['nullable', 'string', 'max:2000'],
        ], [
            'weight.gt' => 'Bobot harus lebih besar dari 0.',
            'weight.lte' => 'Bobot terlalu besar. Gunakan nilai maksimal 99.999,99999.',
        ]);
    }
}
