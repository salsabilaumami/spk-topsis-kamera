<?php

namespace App\Services;

use App\Models\Alternative;
use App\Models\Assessment;
use App\Models\CalculationAlternative;
use App\Models\CalculationCriterion;
use App\Models\CalculationRun;
use App\Models\CalculationValue;
use App\Models\Criterion;
use App\Support\Number;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final class TopsisService
{
    public function __construct(private readonly TopsisCalculator $calculator)
    {
    }

    /** @return array<string, mixed> */
    public function readiness(): array
    {
        $alternativeCount = Alternative::count();
        $criterionCount = Criterion::count();
        $totalWeight = (float) Criterion::sum('weight');
        $expectedValues = $alternativeCount * $criterionCount;
        $assessmentCount = Assessment::count();
        $missingValues = max(0, $expectedValues - $assessmentCount);
        $zeroCriteria = [];

        if ($criterionCount > 0 && $alternativeCount > 0 && $missingValues === 0) {
            $zeroCriteria = Criterion::query()
                ->whereDoesntHave('assessments', fn ($query) => $query->where('value', '>', 0))
                ->pluck('code')
                ->all();
        }

        $checks = [
            'alternatives' => [
                'ok' => $alternativeCount >= 2,
                'message' => $alternativeCount >= 2 ? 'Minimal dua alternatif tersedia.' : 'Tambahkan minimal dua alternatif.',
            ],
            'criteria' => [
                'ok' => $criterionCount > 0,
                'message' => $criterionCount > 0 ? 'Kriteria tersedia.' : 'Tambahkan minimal satu kriteria.',
            ],
            'weights' => [
                'ok' => $criterionCount > 0 && $totalWeight > 0.0,
                'message' => $criterionCount > 0 && $totalWeight > 0.0
                    ? 'Total bobot input '.Number::decimal($totalWeight, 6).' akan dinormalisasi otomatis menjadi 1,00.'
                    : 'Total bobot harus lebih besar dari nol.',
            ],
            'assessments' => [
                'ok' => $expectedValues > 0 && $missingValues === 0,
                'message' => $missingValues === 0 && $expectedValues > 0 ? 'Semua nilai alternatif telah diisi.' : "Masih ada {$missingValues} nilai yang belum diisi.",
            ],
            'divisors' => [
                'ok' => count($zeroCriteria) === 0,
                'message' => count($zeroCriteria) === 0 ? 'Pembagi normalisasi valid.' : 'Semua nilai nol pada: '.implode(', ', $zeroCriteria).'.',
            ],
        ];

        return [
            'ready' => collect($checks)->every(fn (array $check): bool => $check['ok']),
            'alternative_count' => $alternativeCount,
            'criterion_count' => $criterionCount,
            'total_weight' => $totalWeight,
            'assessment_count' => $assessmentCount,
            'expected_values' => $expectedValues,
            'missing_values' => $missingValues,
            'checks' => $checks,
        ];
    }

    public function currentInputHash(): string
    {
        $criteria = Criterion::query()->orderBy('id')->get(['id', 'code', 'name', 'unit', 'type', 'weight']);
        $alternatives = Alternative::query()->orderBy('id')->get(['id', 'code', 'name', 'description']);
        $assessments = Assessment::query()->orderBy('alternative_id')->orderBy('criterion_id')->get(['alternative_id', 'criterion_id', 'value']);

        return hash('sha256', json_encode([
            'criteria' => $criteria->map(fn (Criterion $criterion): array => [
                $criterion->id, $criterion->code, $criterion->name, $criterion->unit,
                $criterion->type, (string) $criterion->weight,
            ])->all(),
            'alternatives' => $alternatives->map(fn (Alternative $alternative): array => [
                $alternative->id, $alternative->code, $alternative->name, $alternative->description,
            ])->all(),
            'assessments' => $assessments->map(fn (Assessment $assessment): array => [
                $assessment->alternative_id, $assessment->criterion_id, (string) $assessment->value,
            ])->all(),
        ], JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION));
    }

    public function isStale(?CalculationRun $run): bool
    {
        return $run !== null && !hash_equals($run->input_hash, $this->currentInputHash());
    }

    public function calculate(?string $name, ?int $userId): CalculationRun
    {
        $readiness = $this->readiness();
        if (!$readiness['ready']) {
            $failed = collect($readiness['checks'])->first(fn (array $check): bool => !$check['ok']);
            throw new DomainException($failed['message'] ?? 'Data belum siap dihitung.');
        }

        $criteriaModels = Criterion::query()->orderBy('id')->get();
        $alternativeModels = Alternative::query()->with('assessments')->orderBy('id')->get();
        $criteria = $criteriaModels->map(fn (Criterion $criterion): array => [
            'id' => $criterion->id,
            'code' => $criterion->code,
            'name' => $criterion->name,
            'unit' => $criterion->unit,
            'type' => $criterion->type,
            'weight' => (float) $criterion->weight,
        ])->all();
        $alternatives = $alternativeModels->map(fn (Alternative $alternative): array => [
            'id' => $alternative->id,
            'code' => $alternative->code,
            'name' => $alternative->name,
            'description' => $alternative->description,
            'values' => $alternative->assessments->mapWithKeys(
                fn (Assessment $assessment): array => [$assessment->criterion_id => (float) $assessment->value]
            )->all(),
        ])->all();

        $calculated = $this->calculator->calculate($criteria, $alternatives);
        $hash = $this->currentInputHash();
        $code = 'TOPSIS-'.now()->format('Ymd-His').'-'.strtoupper(Str::random(4));
        $name = trim((string) $name) !== '' ? trim((string) $name) : 'Perhitungan '.now()->format('d/m/Y H:i');

        try {
            return DB::transaction(function () use ($criteriaModels, $alternativeModels, $calculated, $hash, $code, $name, $userId): CalculationRun {
                $best = $calculated['results'][0];
                $run = CalculationRun::create([
                    'code' => $code,
                    'name' => $name,
                    'alternative_count' => $alternativeModels->count(),
                    'criterion_count' => $criteriaModels->count(),
                    'total_weight' => Number::database($criteriaModels->sum(fn (Criterion $criterion): float => (float) $criterion->weight)),
                    'input_hash' => $hash,
                    'best_alternative_code' => $best['code'],
                    'best_alternative_name' => $best['name'],
                    'best_preference' => Number::database($best['preference']),
                    'status' => 'processing',
                    'processed_by' => $userId,
                ]);

                $criterionSnapshots = [];
                foreach ($criteriaModels as $criterion) {
                    $criterionSnapshots[$criterion->id] = CalculationCriterion::create([
                        'calculation_run_id' => $run->id,
                        'source_criterion_id' => $criterion->id,
                        'code' => $criterion->code,
                        'name' => $criterion->name,
                        'unit' => $criterion->unit,
                        'type' => $criterion->type,
                        'weight' => Number::database($calculated['normalized_weights'][$criterion->id]),
                        'divisor' => Number::database($calculated['divisors'][$criterion->id]),
                        'positive_ideal' => Number::database($calculated['positive_ideals'][$criterion->id]),
                        'negative_ideal' => Number::database($calculated['negative_ideals'][$criterion->id]),
                    ]);
                }

                $resultMap = collect($calculated['results'])->keyBy('alternative_id');
                foreach ($alternativeModels as $alternative) {
                    $result = $resultMap[$alternative->id];
                    $alternativeSnapshot = CalculationAlternative::create([
                        'calculation_run_id' => $run->id,
                        'source_alternative_id' => $alternative->id,
                        'code' => $alternative->code,
                        'name' => $alternative->name,
                        'description' => $alternative->description,
                        'd_positive' => Number::database($result['d_positive']),
                        'd_negative' => Number::database($result['d_negative']),
                        'preference' => Number::database($result['preference']),
                        'rank' => $result['rank'],
                        'recommendation_status' => $result['recommendation_status'],
                    ]);

                    foreach ($criteriaModels as $criterion) {
                        CalculationValue::create([
                            'calculation_alternative_id' => $alternativeSnapshot->id,
                            'calculation_criterion_id' => $criterionSnapshots[$criterion->id]->id,
                            'x_value' => Number::database($calculated['x'][$alternative->id][$criterion->id]),
                            'r_value' => Number::database($calculated['r'][$alternative->id][$criterion->id]),
                            'y_value' => Number::database($calculated['y'][$alternative->id][$criterion->id]),
                        ]);
                    }
                }

                $run->update(['status' => 'completed']);
                return $run->fresh(['criteria', 'alternatives.values']);
            }, 3);
        } catch (Throwable $exception) {
            report($exception);
            throw $exception;
        }
    }
}
