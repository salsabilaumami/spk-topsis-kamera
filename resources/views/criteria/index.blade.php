@extends('layouts.app')
@section('title', 'Kriteria')
@section('page-title', 'Data Kriteria')
@section('page-subtitle', 'Kelola kriteria, satuan, jenis atribut, dan bobot relatif TOPSIS')
@section('content')
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="small text-secondary fw-semibold">TOTAL BOBOT INPUT</div>
                    <div class="display-6 fw-bold">{{ decimal_value($totalWeight, 6) }}</div>
                </div>
                <div class="flex-grow-1" style="max-width:480px">
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle me-1"></i>
                        Bobot boleh memakai skala bebas, misalnya 30, 20, 15. Saat perhitungan, sistem otomatis menormalisasi seluruh bobot menjadi total 1,00.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 d-flex align-items-stretch">
        <a href="{{ route('kriteria.create') }}" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-plus-circle"></i> Tambah Kriteria
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5 class="mb-0 fw-bold">Daftar Kriteria</h5></div>
    <div class="card-body p-0">
        @if($criteria->isEmpty())
            <div class="empty-state">
                <i class="bi bi-sliders"></i>
                <h5 class="mt-3">Belum ada kriteria</h5>
                <a href="{{ route('kriteria.create') }}" class="btn btn-primary mt-2">Tambah Kriteria</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kriteria</th>
                            <th>Satuan</th>
                            <th>Jenis</th>
                            <th>Bobot Input</th>
                            <th>Bobot Normalisasi</th>
                            <th>Nilai Terkait</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($criteria as $criterion)
                        @php($normalizedWeight = $totalWeight > 0 ? (float) $criterion->weight / $totalWeight : 0)
                        <tr>
                            <td><span class="code-badge">{{ $criterion->code }}</span></td>
                            <td>
                                <strong>{{ $criterion->name }}</strong>
                                @if($criterion->description)
                                    <div class="small text-secondary">{{ \Illuminate\Support\Str::limit($criterion->description, 80) }}</div>
                                @endif
                            </td>
                            <td>{{ $criterion->unit ?: '-' }}</td>
                            <td><span class="badge rounded-pill {{ $criterion->type === 'benefit' ? 'badge-benefit' : 'badge-cost' }}">{{ ucfirst($criterion->type) }}</span></td>
                            <td><strong>{{ decimal_value($criterion->weight, 6) }}</strong></td>
                            <td>
                                <strong>{{ decimal_value($normalizedWeight, 6) }}</strong>
                                <div class="small text-secondary">{{ percent_value($normalizedWeight) }}</div>
                            </td>
                            <td>{{ $criterion->assessments_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('kriteria.edit', $criterion) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('kriteria.destroy', $criterion) }}" class="d-inline" data-confirm="Hapus {{ $criterion->code }}? Nilai penilaian terkait akan ikut terhapus, tetapi riwayat perhitungan tetap aman.">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
