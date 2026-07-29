@extends('layouts.app')
@section('title', 'Metode TOPSIS')
@section('page-title', 'Metode TOPSIS')
@section('page-subtitle', 'Konsep dan formula Technique for Order Preference by Similarity to Ideal Solution')
@section('content')
<div class="card hero-card mb-4"><div class="card-body p-4"><h2 class="fw-bold">TOPSIS</h2><p class="mb-0 text-white-50">Metode pengambilan keputusan multikriteria yang memilih alternatif paling dekat dengan solusi ideal positif dan paling jauh dari solusi ideal negatif.</p></div></div>
<div class="row g-4"><div class="col-lg-8"><div class="card"><div class="card-header"><h5 class="mb-0 fw-bold">Sebelas Tahap Perhitungan</h5></div><div class="card-body">@foreach([
['Membentuk matriks keputusan X','Nilai kuantitatif setiap alternatif terhadap setiap kriteria.'],
['Menormalisasi bobot','w′ⱼ = bⱼ / Σbⱼ sehingga total bobot normalisasi menjadi 1,00.'],
['Menghitung pembagi normalisasi','Pembagi setiap kriteria = akar jumlah kuadrat seluruh nilai pada kolom.'],
['Membentuk matriks normalisasi R','rᵢⱼ = xᵢⱼ / √Σxᵢⱼ²'],
['Membentuk matriks terbobot Y','yᵢⱼ = wⱼ × rᵢⱼ'],
['Menentukan solusi ideal positif A+','Benefit mengambil maksimum, cost mengambil minimum.'],
['Menentukan solusi ideal negatif A-','Benefit mengambil minimum, cost mengambil maksimum.'],
['Menghitung jarak ideal positif D+','Dᵢ+ = √Σ(yᵢⱼ − yⱼ+)²'],
['Menghitung jarak ideal negatif D-','Dᵢ- = √Σ(yᵢⱼ − yⱼ-)²'],
['Menghitung nilai preferensi V','Vᵢ = Dᵢ- / (Dᵢ+ + Dᵢ-)'],
['Melakukan perangkingan','Nilai V terbesar menjadi alternatif terbaik.']
] as $index => $item)<div class="d-flex gap-3 py-3 {{ !$loop->last ? 'border-bottom' : '' }}"><span class="code-badge">{{ $index+1 }}</span><div><strong>{{ $item[0] }}</strong><div class="text-secondary small mt-1">{{ $item[1] }}</div></div></div>@endforeach</div></div></div><div class="col-lg-4"><div class="card mb-4"><div class="card-header"><h5 class="mb-0 fw-bold">Benefit</h5></div><div class="card-body"><span class="badge badge-benefit rounded-pill mb-2">Nilai besar lebih baik</span><p class="small text-secondary mb-0">Contoh: resolusi efektif, daya tahan baterai, dan kecepatan burst.</p></div></div><div class="card"><div class="card-header"><h5 class="mb-0 fw-bold">Cost</h5></div><div class="card-body"><span class="badge badge-cost rounded-pill mb-2">Nilai kecil lebih baik</span><p class="small text-secondary mb-0">Contoh: harga bodi dan berat operasional kamera.</p></div></div></div></div>
@endsection
