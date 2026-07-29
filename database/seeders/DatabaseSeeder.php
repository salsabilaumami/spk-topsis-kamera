<?php

namespace Database\Seeders;

use App\Models\Alternative;
use App\Models\Assessment;
use App\Models\Criterion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@spkkamera.test'],
            ['name' => 'Administrator SPK', 'password' => Hash::make('admin12345')]
        );

        $criteriaData = [
            [
                'code' => 'C1',
                'name' => 'Harga bodi',
                'unit' => 'juta rupiah',
                'type' => 'cost',
                'weight' => '30.000000000000000',
                'description' => 'Harga kamera body only. Nilai lebih kecil lebih diinginkan.',
            ],
            [
                'code' => 'C2',
                'name' => 'Resolusi efektif',
                'unit' => 'megapiksel (MP)',
                'type' => 'benefit',
                'weight' => '20.000000000000000',
                'description' => 'Resolusi efektif sensor kamera. Nilai lebih besar lebih diinginkan.',
            ],
            [
                'code' => 'C3',
                'name' => 'Berat operasional',
                'unit' => 'gram',
                'type' => 'cost',
                'weight' => '15.000000000000000',
                'description' => 'Berat kamera termasuk baterai dan kartu memori. Nilai lebih kecil lebih diinginkan.',
            ],
            [
                'code' => 'C4',
                'name' => 'Daya tahan baterai',
                'unit' => 'jumlah foto',
                'type' => 'benefit',
                'weight' => '15.000000000000000',
                'description' => 'Perkiraan jumlah foto per pengisian baterai. Nilai lebih besar lebih diinginkan.',
            ],
            [
                'code' => 'C5',
                'name' => 'Kecepatan burst maksimum',
                'unit' => 'frame per second (fps)',
                'type' => 'benefit',
                'weight' => '20.000000000000000',
                'description' => 'Kecepatan pemotretan beruntun maksimum. Nilai lebih besar lebih diinginkan.',
            ],
        ];

        $alternativeData = [
            ['code' => 'A1', 'name' => 'Canon EOS R50', 'description' => 'Kamera mirrorless APS-C ringkas untuk fotografer pemula.'],
            ['code' => 'A2', 'name' => 'Sony Alpha a6400', 'description' => 'Kamera mirrorless APS-C dengan sistem autofocus cepat.'],
            ['code' => 'A3', 'name' => 'Nikon Z50II', 'description' => 'Kamera mirrorless APS-C generasi kedua dengan burst tinggi.'],
            ['code' => 'A4', 'name' => 'Canon EOS R10', 'description' => 'Kamera mirrorless APS-C serbaguna dengan performa pemotretan cepat.'],
            ['code' => 'A5', 'name' => 'Fujifilm X-M5', 'description' => 'Kamera mirrorless APS-C ringan dengan resolusi dan burst tinggi.'],
        ];

        $criteria = [];
        foreach ($criteriaData as $data) {
            $criterion = Criterion::unguarded(
                fn () => Criterion::updateOrCreate(['code' => $data['code']], $data)
            );
            $criteria[$data['code']] = $criterion;
        }

        $alternatives = [];
        foreach ($alternativeData as $data) {
            $alternative = Alternative::unguarded(
                fn () => Alternative::updateOrCreate(['code' => $data['code']], $data)
            );
            $alternatives[$data['code']] = $alternative;
        }

        $values = [
            'A1' => ['C1' => 11.999, 'C2' => 24.2, 'C3' => 375, 'C4' => 440, 'C5' => 15],
            'A2' => ['C1' => 10.699, 'C2' => 24.2, 'C3' => 403, 'C4' => 410, 'C5' => 11],
            'A3' => ['C1' => 17.499, 'C2' => 20.9, 'C3' => 550, 'C4' => 250, 'C5' => 30],
            'A4' => ['C1' => 17.999, 'C2' => 24.2, 'C3' => 429, 'C4' => 430, 'C5' => 23],
            'A5' => ['C1' => 13.499, 'C2' => 26.1, 'C3' => 355, 'C4' => 440, 'C5' => 30],
        ];

        foreach ($values as $alternativeCode => $criterionValues) {
            foreach ($criterionValues as $criterionCode => $value) {
                Assessment::updateOrCreate(
                    [
                        'alternative_id' => $alternatives[$alternativeCode]->id,
                        'criterion_id' => $criteria[$criterionCode]->id,
                    ],
                    ['value' => $value]
                );
            }
        }
    }
}
