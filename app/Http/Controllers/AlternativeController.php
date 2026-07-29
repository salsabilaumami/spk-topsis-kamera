<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlternativeController extends Controller
{
    public function index(): View
    {
        return view('alternatives.index', [
            'alternatives' => Alternative::query()->withCount('assessments')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('alternatives.form', ['alternative' => new Alternative()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Alternative::create($this->validated($request));
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil ditambahkan. Kode dibuat otomatis.');
    }

    public function edit(Alternative $alternative): View
    {
        return view('alternatives.form', compact('alternative'));
    }

    public function update(Request $request, Alternative $alternative): RedirectResponse
    {
        $alternative->update($this->validated($request));
        return redirect()->route('alternatif.index')->with('success', 'Alternatif berhasil diperbarui.');
    }

    public function destroy(Alternative $alternative): RedirectResponse
    {
        $assessmentCount = $alternative->assessments()->count();
        $alternative->delete();

        return redirect()->route('alternatif.index')->with(
            'success',
            $assessmentCount > 0
                ? "Alternatif dihapus bersama {$assessmentCount} nilai terkait. Riwayat perhitungan tetap tersimpan."
                : 'Alternatif berhasil dihapus.'
        );
    }

    /** @return array<string, mixed> */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:3000'],
        ]);
    }
}
