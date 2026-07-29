@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Ringkasan data dan hasil pemilihan kamera mirrorless terbaik')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-camera"></i></div><div><div class="stat-label">Alternatif</div><div class="stat-value">{{ $alternativeCount }}</div></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-sliders"></i></div><div><div class="stat-label">Kriteria</div><div class="stat-value">{{ $criterionCount }}</div></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-percent"></i></div><div><div class="stat-label">Bobot Input</div><div class="stat-value">{{ decimal_value($totalWeight, 2) }}</div></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i class="bi bi-calculator"></i></div><div><div class="stat-label">Perhitungan</div><div class="stat-value">{{ $calculationCount }}</div></div></div></div></div>
</div>

@if($latestRun)
    <div class="{{ $isStale ? 'stale-banner' : 'fresh-banner' }} mb-4 d-flex gap-2 align-items-start">
        <i class="bi {{ $isStale ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }} mt-1"></i>
        <div><strong>{{ $isStale ? 'Hasil terakhir sudah tidak sesuai data terbaru.' : 'Hasil terakhir masih sesuai dengan data saat ini.' }}</strong><div class="small">{{ $isStale ? 'Bobot, alternatif, kriteria, atau nilai telah berubah. Jalankan perhitungan ulang.' : 'Input hash hasil terakhir sama dengan data master dan penilaian saat ini.' }}</div></div>
    </div>
@endif

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card hero-card mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-md-8">
                        <span class="badge text-bg-warning text-dark mb-3">Rekomendasi Terbaru</span>
                        @if($latestRun)
                            <h2 class="fw-bold mb-2">{{ $latestRun->best_alternative_name }}</h2>
                            <p class="mb-3 text-white-50">{{ $latestRun->best_alternative_code }} menjadi peringkat pertama dengan nilai preferensi <strong class="text-white">{{ decimal_value($latestRun->best_preference) }}</strong>.</p>
                            <a href="{{ route('results.show', $latestRun) }}" class="btn btn-light"><i class="bi bi-trophy me-1"></i> Lihat Hasil Lengkap</a>
                        @else
                            <h2 class="fw-bold mb-2">Belum ada hasil perhitungan</h2>
                            <p class="mb-3 text-white-50">Lengkapi data penilaian dan jalankan TOPSIS untuk mendapatkan kamera terbaik.</p>
                            <a href="{{ route('process.index') }}" class="btn btn-light">Mulai Perhitungan</a>
                        @endif
                    </div>
                    <div class="col-md-4 text-center"><i class="bi bi-award" style="font-size:7rem;color:#f7d875"></i></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between"><div><h5 class="mb-1 fw-bold">Grafik Ranking Terakhir</h5><div class="small text-secondary">Nilai preferensi semakin besar berarti semakin baik.</div></div>@if($latestRun)<span class="badge text-bg-light">{{ $latestRun->code }}</span>@endif</div>
            <div class="card-body">
                @if($latestRun && $latestRun->alternatives->isNotEmpty())
                    <div style="height:340px"><canvas id="rankingChart"></canvas></div>
                @else
                    <div class="empty-state"><i class="bi bi-bar-chart"></i><h5 class="mt-3">Grafik belum tersedia</h5><p class="text-secondary mb-0">Lakukan perhitungan TOPSIS terlebih dahulu.</p></div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0 fw-bold">Kesiapan Perhitungan</h5></div>
            <div class="card-body">
                @foreach($readiness['checks'] as $check)
                    <div class="readiness-item"><span class="status-dot {{ $check['ok'] ? 'ok' : 'no' }}"><i class="bi {{ $check['ok'] ? 'bi-check' : 'bi-x' }}"></i></span><div><strong>{{ $check['ok'] ? 'Siap' : 'Belum siap' }}</strong><div class="small text-secondary">{{ $check['message'] }}</div></div></div>
                @endforeach
                <a href="{{ route('process.index') }}" class="btn btn-primary w-100 mt-3 {{ !$readiness['ready'] ? 'disabled' : '' }}"><i class="bi bi-calculator me-1"></i> Proses TOPSIS</a>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0 fw-bold">Akses Cepat</h5></div>
            <div class="card-body d-grid gap-2">
                <a class="quick-link" href="{{ route('kriteria.index') }}"><i class="bi bi-sliders"></i><div><strong>Kriteria</strong><div class="small text-secondary">Atur jenis dan bobot relatif</div></div></a>
                <a class="quick-link" href="{{ route('alternatif.index') }}"><i class="bi bi-camera"></i><div><strong>Alternatif</strong><div class="small text-secondary">Kelola kamera mirrorless</div></div></a>
                <a class="quick-link" href="{{ route('assessments.index') }}"><i class="bi bi-table"></i><div><strong>Input Penilaian</strong><div class="small text-secondary">Isi matriks keputusan</div></div></a>
                <a class="quick-link" href="{{ route('history.index') }}"><i class="bi bi-clock-history"></i><div><strong>Riwayat</strong><div class="small text-secondary">Lihat proses tersimpan</div></div></a>
            </div>
        </div>
    </div>
</div>
@endsection

@if($latestRun && $latestRun->alternatives->isNotEmpty())
@push('scripts')
<script type="application/json" id="ranking-chart-payload">{!! Illuminate\Support\Js::encode(['labels' => $chartLabels, 'values' => $chartValues]) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payloadElement = document.getElementById('ranking-chart-payload');
    const canvas = document.getElementById('rankingChart');

    if (!payloadElement || !canvas) return;

    const payload = JSON.parse(payloadElement.textContent);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: payload.labels,
            datasets: [{ label: 'Nilai Preferensi', data: payload.values, borderWidth: 1, borderRadius: 8 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, max: 1 } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush
@endif
