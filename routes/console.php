<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('spk:info', function () {
    $this->info('SPK TOPSIS Pemilihan Kamera Mirrorless Terbaik untuk Fotografer Pemula');
})->purpose('Menampilkan identitas aplikasi');
