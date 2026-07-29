<?php

return [
    'accepted' => ':attribute harus diterima.',
    'array' => ':attribute harus berupa array.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
        'array' => ':attribute maksimal memiliki :max item.',
    ],
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
        'array' => ':attribute minimal memiliki :min item.',
    ],
    'numeric' => ':attribute harus berupa angka.',
    'required' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'gt' => [
        'numeric' => ':attribute harus lebih besar dari :value.',
    ],
    'lte' => [
        'numeric' => ':attribute harus kurang dari atau sama dengan :value.',
    ],
    'custom' => [],
    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'password' => 'kata sandi',
        'unit' => 'satuan',
        'type' => 'jenis',
        'weight' => 'bobot',
        'description' => 'keterangan',
        'values' => 'nilai penilaian',
    ],
];
