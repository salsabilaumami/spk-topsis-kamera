<?php

namespace App\Services;

use DomainException;

final class TopsisCalculator
{
    /**
     * @param array<int, array{id:int|string, code:string, name:string, type:string, weight:float|int|string}> $criteria
     * @param array<int, array{id:int|string, code:string, name:string, description?:?string, values:array<int|string, float|int|string>}> $alternatives
     * @return array<string, mixed>
     */
    public function calculate(array $criteria, array $alternatives): array
    {
        if (count($criteria) === 0) {
            throw new DomainException('Kriteria belum tersedia.');
        }

        if (count($alternatives) < 2) {
            throw new DomainException('Minimal diperlukan dua alternatif untuk perhitungan TOPSIS.');
        }

        $totalWeight = 0.0;
        $normalizedWeights = [];

        foreach ($criteria as $criterion) {
            $weight = (float) $criterion['weight'];

            if (!is_finite($weight) || $weight <= 0.0) {
                throw new DomainException("Bobot {$criterion['code']} harus lebih besar dari nol.");
            }

            $totalWeight += $weight;
        }

        if (!is_finite($totalWeight) || $totalWeight <= 0.0) {
            throw new DomainException('Total bobot harus lebih besar dari nol.');
        }

        foreach ($criteria as $criterion) {
            $normalizedWeights[$criterion['id']] = (float) $criterion['weight'] / $totalWeight;
        }

        $x = [];
        $divisors = [];
        $r = [];
        $y = [];
        $positiveIdeals = [];
        $negativeIdeals = [];

        foreach ($criteria as $criterion) {
            $criterionId = $criterion['id'];
            $sumSquares = 0.0;

            foreach ($alternatives as $alternative) {
                if (!array_key_exists($criterionId, $alternative['values'])) {
                    throw new DomainException("Nilai {$alternative['code']} terhadap {$criterion['code']} belum diisi.");
                }

                $value = (float) $alternative['values'][$criterionId];
                if (!is_finite($value) || $value < 0) {
                    throw new DomainException("Nilai {$alternative['code']} terhadap {$criterion['code']} tidak valid.");
                }

                $x[$alternative['id']][$criterionId] = $value;
                $sumSquares += $value ** 2;
            }

            $divisor = sqrt($sumSquares);
            if ($divisor <= 0.0) {
                throw new DomainException("Pembagi normalisasi {$criterion['code']} bernilai nol. Isi minimal satu nilai lebih besar dari nol.");
            }

            $divisors[$criterionId] = $divisor;
        }

        foreach ($alternatives as $alternative) {
            foreach ($criteria as $criterion) {
                $criterionId = $criterion['id'];
                $r[$alternative['id']][$criterionId] = $x[$alternative['id']][$criterionId] / $divisors[$criterionId];
                $y[$alternative['id']][$criterionId] = $normalizedWeights[$criterionId] * $r[$alternative['id']][$criterionId];
            }
        }

        foreach ($criteria as $criterion) {
            $criterionId = $criterion['id'];
            $column = array_map(fn (array $alternative): float => $y[$alternative['id']][$criterionId], $alternatives);

            if ($criterion['type'] === 'benefit') {
                $positiveIdeals[$criterionId] = max($column);
                $negativeIdeals[$criterionId] = min($column);
            } elseif ($criterion['type'] === 'cost') {
                $positiveIdeals[$criterionId] = min($column);
                $negativeIdeals[$criterionId] = max($column);
            } else {
                throw new DomainException("Jenis kriteria {$criterion['code']} harus benefit atau cost.");
            }
        }

        $results = [];
        foreach ($alternatives as $alternative) {
            $positiveSquare = 0.0;
            $negativeSquare = 0.0;

            foreach ($criteria as $criterion) {
                $criterionId = $criterion['id'];
                $positiveSquare += ($y[$alternative['id']][$criterionId] - $positiveIdeals[$criterionId]) ** 2;
                $negativeSquare += ($y[$alternative['id']][$criterionId] - $negativeIdeals[$criterionId]) ** 2;
            }

            $dPositive = sqrt($positiveSquare);
            $dNegative = sqrt($negativeSquare);
            $denominator = $dPositive + $dNegative;
            $preference = $denominator > 0.0 ? $dNegative / $denominator : 0.5;

            $results[] = [
                'alternative_id' => $alternative['id'],
                'code' => $alternative['code'],
                'name' => $alternative['name'],
                'description' => $alternative['description'] ?? null,
                'd_positive' => $dPositive,
                'd_negative' => $dNegative,
                'preference' => $preference,
            ];
        }

        usort($results, function (array $left, array $right): int {
            $comparison = $right['preference'] <=> $left['preference'];
            return $comparison !== 0 ? $comparison : strnatcasecmp($left['code'], $right['code']);
        });

        foreach ($results as $index => &$result) {
            $result['rank'] = $index + 1;
            $result['recommendation_status'] = match ($index + 1) {
                1 => 'Sangat direkomendasikan',
                2 => 'Direkomendasikan',
                3 => 'Dapat dipertimbangkan',
                default => 'Alternatif lainnya',
            };
        }
        unset($result);

        return [
            'raw_total_weight' => $totalWeight,
            'normalized_weights' => $normalizedWeights,
            'x' => $x,
            'divisors' => $divisors,
            'r' => $r,
            'y' => $y,
            'positive_ideals' => $positiveIdeals,
            'negative_ideals' => $negativeIdeals,
            'results' => $results,
        ];
    }
}
