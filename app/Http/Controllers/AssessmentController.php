<?php

namespace App\Http\Controllers;

use App\Models\Alternative;
use App\Models\Assessment;
use App\Models\Criterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $alternatives = Alternative::query()->orderBy('id')->get();
        $criteria = Criterion::query()->orderBy('id')->get();
        $values = Assessment::query()->get()->groupBy('alternative_id')
            ->map(fn ($rows) => $rows->pluck('value', 'criterion_id'));

        return view('assessments.index', compact('alternatives', 'criteria', 'values'));
    }

    public function store(Request $request): RedirectResponse
    {
        $alternatives = Alternative::query()->orderBy('id')->get();
        $criteria = Criterion::query()->orderBy('id')->get();

        if ($alternatives->isEmpty() || $criteria->isEmpty()) {
            return back()->with('error', 'Alternatif dan kriteria harus tersedia sebelum menyimpan penilaian.');
        }

        $rules = ['values' => ['required', 'array']];
        $attributes = [];
        foreach ($alternatives as $alternative) {
            foreach ($criteria as $criterion) {
                $key = "values.{$alternative->id}.{$criterion->id}";
                $rules[$key] = ['required', 'numeric', 'min:0'];
                $attributes[$key] = "nilai {$alternative->code} - {$criterion->code}";
            }
        }

        $validated = Validator::make($request->all(), $rules, [], $attributes)->validate();

        DB::transaction(function () use ($alternatives, $criteria, $validated): void {
            foreach ($alternatives as $alternative) {
                foreach ($criteria as $criterion) {
                    Assessment::updateOrCreate(
                        ['alternative_id' => $alternative->id, 'criterion_id' => $criterion->id],
                        ['value' => $validated['values'][$alternative->id][$criterion->id]]
                    );
                }
            }
        }, 3);

        return redirect()->route('assessments.index')->with('success', 'Seluruh nilai alternatif berhasil disimpan. Hasil lama perlu dihitung ulang.');
    }

    public function reset(): RedirectResponse
    {
        Assessment::query()->delete();
        return redirect()->route('assessments.index')->with('success', 'Seluruh nilai penilaian berhasil dikosongkan.');
    }
}
