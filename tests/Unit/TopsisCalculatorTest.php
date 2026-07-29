<?php

namespace Tests\Unit;

use App\Services\TopsisCalculator;
use PHPUnit\Framework\TestCase;

class TopsisCalculatorTest extends TestCase
{
    public function test_camera_dataset_produces_expected_ranking_with_relative_weights(): void
    {
        $criteria = [
            ['id' => 1, 'code' => 'C1', 'name' => 'Harga bodi', 'type' => 'cost', 'weight' => 30],
            ['id' => 2, 'code' => 'C2', 'name' => 'Resolusi efektif', 'type' => 'benefit', 'weight' => 20],
            ['id' => 3, 'code' => 'C3', 'name' => 'Berat operasional', 'type' => 'cost', 'weight' => 15],
            ['id' => 4, 'code' => 'C4', 'name' => 'Daya tahan baterai', 'type' => 'benefit', 'weight' => 15],
            ['id' => 5, 'code' => 'C5', 'name' => 'Kecepatan burst maksimum', 'type' => 'benefit', 'weight' => 20],
        ];

        $alternatives = [
            ['id' => 1, 'code' => 'A1', 'name' => 'Canon EOS R50', 'values' => [1 => 11.999, 2 => 24.2, 3 => 375, 4 => 440, 5 => 15]],
            ['id' => 2, 'code' => 'A2', 'name' => 'Sony Alpha a6400', 'values' => [1 => 10.699, 2 => 24.2, 3 => 403, 4 => 410, 5 => 11]],
            ['id' => 3, 'code' => 'A3', 'name' => 'Nikon Z50II', 'values' => [1 => 17.499, 2 => 20.9, 3 => 550, 4 => 250, 5 => 30]],
            ['id' => 4, 'code' => 'A4', 'name' => 'Canon EOS R10', 'values' => [1 => 17.999, 2 => 24.2, 3 => 429, 4 => 430, 5 => 23]],
            ['id' => 5, 'code' => 'A5', 'name' => 'Fujifilm X-M5', 'values' => [1 => 13.499, 2 => 26.1, 3 => 355, 4 => 440, 5 => 30]],
        ];

        $result = (new TopsisCalculator())->calculate($criteria, $alternatives);

        self::assertSame(['A5', 'A1', 'A2', 'A3', 'A4'], array_column($result['results'], 'code'));
        self::assertEqualsWithDelta(0.790863, $result['results'][0]['preference'], 0.000001);
        self::assertEqualsWithDelta(0.546536, $result['results'][1]['preference'], 0.000001);
        self::assertEqualsWithDelta(100.0, $result['raw_total_weight'], 0.0000001);
        self::assertEqualsWithDelta(1.0, array_sum($result['normalized_weights']), 0.0000001);
        self::assertEqualsWithDelta(0.30, $result['normalized_weights'][1], 0.0000001);
        self::assertSame('Sangat direkomendasikan', $result['results'][0]['recommendation_status']);
    }

    public function test_weight_scale_does_not_change_ranking(): void
    {
        $criteriaA = [
            ['id' => 1, 'code' => 'C1', 'name' => 'Harga', 'type' => 'cost', 'weight' => 30],
            ['id' => 2, 'code' => 'C2', 'name' => 'Resolusi', 'type' => 'benefit', 'weight' => 20],
        ];
        $criteriaB = [
            ['id' => 1, 'code' => 'C1', 'name' => 'Harga', 'type' => 'cost', 'weight' => 3],
            ['id' => 2, 'code' => 'C2', 'name' => 'Resolusi', 'type' => 'benefit', 'weight' => 2],
        ];
        $alternatives = [
            ['id' => 1, 'code' => 'A1', 'name' => 'Kamera A', 'values' => [1 => 10, 2 => 20]],
            ['id' => 2, 'code' => 'A2', 'name' => 'Kamera B', 'values' => [1 => 12, 2 => 24]],
        ];

        $calculator = new TopsisCalculator();
        $resultA = $calculator->calculate($criteriaA, $alternatives);
        $resultB = $calculator->calculate($criteriaB, $alternatives);

        self::assertSame(array_column($resultA['results'], 'code'), array_column($resultB['results'], 'code'));
        self::assertEqualsWithDelta($resultA['results'][0]['preference'], $resultB['results'][0]['preference'], 0.0000000001);
    }
}
