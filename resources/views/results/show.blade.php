@extends('layouts.app')

@section('title', 'Hasil Ranking')
@section('page-title', 'Hasil Ranking TOPSIS')
@section('page-subtitle', $run->name.' · '.$run->code)

@section('content')
<div class="{{ $isStale ? 'stale-banner' : 'fresh-banner' }} mb-4 d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
    <div class="d-flex gap-2">
        <i class="bi {{ $isStale ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }}"></i>
        <div>
            <strong>{{ $isStale ? 'Hasil ini adalah snapshot lama.' : 'Hasil ini sesuai dengan data saat ini.' }}</strong>
            <div class="small">
                {{ $isStale
                    ? 'Data master atau penilaian sudah berubah. Snapshot tetap valid untuk riwayat, tetapi keputusan terbaru memerlukan hitung ulang.'
                    : 'Tidak ada perubahan input sejak perhitungan dibuat.' }}
            </div>
        </div>
    </div>
    @if($isStale)
        <a href="{{ route('process.index') }}" class="btn btn-warning btn-sm">Hitung Ulang</a>
    @endif
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="card best-card h-100">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge text-bg-warning mb-3"><i class="bi bi-trophy-fill me-1"></i>Peringkat 1</span>
                        <h2 class="fw-bold mb-1">{{ $run->alternatives->first()->name }}</h2>
                        <div class="text-secondary mb-3">
                            {{ $run->alternatives->first()->code }} · {{ $run->alternatives->first()->recommendation_status }}
                        </div>
                        <div class="d-flex align-items-end gap-2">
                            <div class="display-5 fw-bold">{{ decimal_value($run->alternatives->first()->preference) }}</div>
                            <div class="text-secondary mb-2">nilai preferensi</div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-camera-fill" style="font-size:7rem;color:#d6a719"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0 fw-bold">Informasi Proses</h5></div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary">Tanggal</span><strong>{{ $run->created_at->format('d/m/Y H:i') }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary">Alternatif</span><strong>{{ $run->alternative_count }}</strong></div>
                <div class="d-flex justify-content-between py-2 border-bottom"><span class="text-secondary">Kriteria</span><strong>{{ $run->criterion_count }}</strong></div>
                <div class="d-flex justify-content-between py-2"><span class="text-secondary">Total bobot input</span><strong>{{ decimal_value($run->total_weight) }}</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('results.print', $run) }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-printer me-1"></i> Cetak</a>
    <a href="{{ route('results.pdf', $run) }}" class="btn btn-outline-danger"><i class="bi bi-file-earmark-pdf me-1"></i> Ekspor PDF</a>
    <a href="{{ route('results.csv', $run) }}" class="btn btn-outline-success"><i class="bi bi-file-earmark-spreadsheet me-1"></i> Ekspor CSV</a>
    <a href="#detail-perhitungan" class="btn btn-primary"><i class="bi bi-calculator me-1"></i> Detail Perhitungan</a>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0 fw-bold">Tabel Ranking</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Ranking</th><th>Alternatif</th><th>D+</th><th>D-</th><th>Preferensi</th><th>Status</th></tr></thead>
                        <tbody>
                        @foreach($run->alternatives as $alternative)
                            <tr>
                                <td><span class="ranking-number rank-{{ $alternative->rank }}">{{ $alternative->rank }}</span></td>
                                <td><strong>{{ $alternative->code }} · {{ $alternative->name }}</strong></td>
                                <td>{{ decimal_value($alternative->d_positive) }}</td>
                                <td>{{ decimal_value($alternative->d_negative) }}</td>
                                <td><strong>{{ decimal_value($alternative->preference) }}</strong></td>
                                <td><span class="badge rounded-pill {{ $alternative->rank === 1 ? 'text-bg-success' : ($alternative->rank === 2 ? 'text-bg-primary' : 'text-bg-light') }}">{{ $alternative->recommendation_status }}</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0 fw-bold">Grafik Nilai Preferensi</h5></div>
            <div class="card-body"><div style="height:360px"><canvas id="resultChart"></canvas></div></div>
        </div>
    </div>
</div>

<div id="detail-perhitungan" class="row g-4">
    <div class="col-lg-3">
        <div class="card stage-nav">
            <div class="card-header"><h6 class="mb-0 fw-bold">Navigasi Tahapan</h6></div>
            <div class="card-body p-2">
                <div class="list-group">
                    @foreach([
                        'x' => '1. Matriks X',
                        'weights' => '2. Normalisasi Bobot',
                        'divisor' => '3. Pembagi',
                        'r' => '4. Matriks R',
                        'y' => '5. Matriks Y',
                        'aplus' => '6. Ideal A+',
                        'aminus' => '7. Ideal A-',
                        'dplus' => '8. Jarak D+',
                        'dminus' => '9. Jarak D-',
                        'preference' => '10. Preferensi V',
                        'ranking' => '11. Ranking',
                    ] as $anchor => $label)
                        <a class="list-group-item list-group-item-action" href="#{{ $anchor }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <section id="x" class="card matrix-section mb-4">
            <div class="card-header">
                <h5 class="mb-1 fw-bold">Tahap 1 · Matriks Keputusan X</h5>
                <div class="small text-secondary">Nilai kuantitatif setiap alternatif terhadap setiap kriteria.</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table matrix-table">
                        <thead><tr><th>Alternatif</th>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }}<div class="fw-normal">{{ $criterion->name }}</div></th>@endforeach</tr></thead>
                        <tbody>
                        @foreach($run->alternatives->sortBy('code', SORT_NATURAL) as $alternative)
                            <tr>
                                <td><strong>{{ $alternative->code }}</strong><div class="small text-secondary">{{ $alternative->name }}</div></td>
                                @foreach($run->criteria as $criterion)
                                    <td>{{ decimal_value(data_get($valueMap, $alternative->id.'.'.$criterion->id.'.x_value')) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="weights" class="card matrix-section mb-4">
            <div class="card-header">
                <h5 class="mb-1 fw-bold">Tahap 2 · Normalisasi Bobot</h5>
                <div class="small text-secondary">Bobot input dibagi total bobot input. Total bobot normalisasi = 1,00.</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }}<div class="fw-normal">{{ $criterion->name }}</div></th>@endforeach</tr></thead>
                        <tbody><tr>@foreach($run->criteria as $criterion)<td><strong>{{ decimal_value($criterion->weight) }}</strong><div class="small text-secondary">{{ percent_value($criterion->weight) }}</div></td>@endforeach</tr></tbody>
                    </table>
                </div>
            </div>
        </section>

        <section id="divisor" class="card matrix-section mb-4">
            <div class="card-header">
                <h5 class="mb-1 fw-bold">Tahap 3 · Pembagi Normalisasi</h5>
                <div class="small text-secondary">√Σxᵢⱼ² untuk setiap kriteria.</div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table"><thead><tr>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }}</th>@endforeach</tr></thead><tbody><tr>@foreach($run->criteria as $criterion)<td><strong>{{ decimal_value($criterion->divisor) }}</strong></td>@endforeach</tr></tbody></table>
                </div>
            </div>
        </section>

        @foreach([
            'r' => ['Tahap 4 · Matriks Normalisasi R', 'r_value', 'rᵢⱼ = xᵢⱼ / pembagi kriteria.'],
            'y' => ['Tahap 5 · Matriks Normalisasi Terbobot Y', 'y_value', 'yᵢⱼ = wⱼ × rᵢⱼ dengan bobot yang sudah dinormalisasi.'],
        ] as $anchor => $stage)
            <section id="{{ $anchor }}" class="card matrix-section mb-4">
                <div class="card-header"><h5 class="mb-1 fw-bold">{{ $stage[0] }}</h5><div class="small text-secondary">{{ $stage[2] }}</div></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table matrix-table">
                            <thead><tr><th>Alternatif</th>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }}<div class="fw-normal">{{ $criterion->name }}</div></th>@endforeach</tr></thead>
                            <tbody>
                            @foreach($run->alternatives->sortBy('code', SORT_NATURAL) as $alternative)
                                <tr>
                                    <td><strong>{{ $alternative->code }}</strong><div class="small text-secondary">{{ $alternative->name }}</div></td>
                                    @foreach($run->criteria as $criterion)
                                        <td>{{ decimal_value(data_get($valueMap, $alternative->id.'.'.$criterion->id.'.'.$stage[1])) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        @endforeach

        <section id="aplus" class="card matrix-section mb-4">
            <div class="card-header"><h5 class="mb-1 fw-bold">Tahap 6 · Solusi Ideal Positif A+</h5><div class="small text-secondary">Benefit = maksimum; cost = minimum.</div></div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table"><thead><tr>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }} <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }}">{{ $criterion->type }}</span></th>@endforeach</tr></thead><tbody><tr>@foreach($run->criteria as $criterion)<td><strong>{{ decimal_value($criterion->positive_ideal) }}</strong></td>@endforeach</tr></tbody></table></div></div>
        </section>

        <section id="aminus" class="card matrix-section mb-4">
            <div class="card-header"><h5 class="mb-1 fw-bold">Tahap 7 · Solusi Ideal Negatif A-</h5><div class="small text-secondary">Benefit = minimum; cost = maksimum.</div></div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table"><thead><tr>@foreach($run->criteria as $criterion)<th>{{ $criterion->code }} <span class="badge {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }}">{{ $criterion->type }}</span></th>@endforeach</tr></thead><tbody><tr>@foreach($run->criteria as $criterion)<td><strong>{{ decimal_value($criterion->negative_ideal) }}</strong></td>@endforeach</tr></tbody></table></div></div>
        </section>

        @foreach([
            'dplus' => ['Tahap 8 · Jarak Solusi Ideal Positif D+', 'd_positive'],
            'dminus' => ['Tahap 9 · Jarak Solusi Ideal Negatif D-', 'd_negative'],
            'preference' => ['Tahap 10 · Nilai Preferensi V', 'preference'],
        ] as $anchor => $stage)
            <section id="{{ $anchor }}" class="card matrix-section mb-4">
                <div class="card-header"><h5 class="mb-0 fw-bold">{{ $stage[0] }}</h5></div>
                <div class="card-body p-0"><div class="table-responsive"><table class="table"><thead><tr><th>Alternatif</th><th>Nilai</th></tr></thead><tbody>@foreach($run->alternatives->sortBy('code', SORT_NATURAL) as $alternative)<tr><td><strong>{{ $alternative->code }} · {{ $alternative->name }}</strong></td><td><strong>{{ decimal_value($alternative->{$stage[1]}) }}</strong></td></tr>@endforeach</tbody></table></div></div>
            </section>
        @endforeach

        <section id="ranking" class="card matrix-section">
            <div class="card-header"><h5 class="mb-1 fw-bold">Tahap 11 · Perangkingan</h5><div class="small text-secondary">Alternatif dengan nilai preferensi terbesar menjadi rekomendasi terbaik.</div></div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table"><thead><tr><th>Rank</th><th>Kode</th><th>Nama Kamera</th><th>Preferensi</th><th>Rekomendasi</th></tr></thead><tbody>@foreach($run->alternatives as $alternative)<tr><td><span class="ranking-number rank-{{ $alternative->rank }}">{{ $alternative->rank }}</span></td><td>{{ $alternative->code }}</td><td><strong>{{ $alternative->name }}</strong></td><td>{{ decimal_value($alternative->preference) }}</td><td>{{ $alternative->recommendation_status }}</td></tr>@endforeach</tbody></table></div></div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script type="application/json" id="result-chart-payload">{!! Illuminate\Support\Js::encode(['labels' => $chartLabels, 'values' => $chartValues]) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payloadElement = document.getElementById('result-chart-payload');
    const canvas = document.getElementById('resultChart');

    if (!payloadElement || !canvas) return;

    const payload = JSON.parse(payloadElement.textContent);

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: payload.labels,
            datasets: [{
                label: 'Nilai Preferensi',
                data: payload.values,
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: { x: { beginAtZero: true, max: 1 } },
            plugins: { legend: { display: false } }
        }
    });
});
</script>
@endpush
