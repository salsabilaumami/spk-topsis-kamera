@extends('layouts.app')
@section('title', 'Alternatif')
@section('page-title', 'Data Alternatif')
@section('page-subtitle', 'Kelola seluruh kamera mirrorless yang akan dibandingkan')
@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h5 class="mb-1 fw-bold">{{ $alternatives->count() }} kamera tersedia</h5>
        <div class="text-secondary small">Jumlah alternatif tidak dibatasi dan kode dibuat otomatis.</div>
    </div>
    <a href="{{ route('alternatif.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Tambah Alternatif</a>
</div>

<div class="card">
    <div class="card-body p-0">
        @if($alternatives->isEmpty())
            <div class="empty-state">
                <i class="bi bi-camera"></i>
                <h5 class="mt-3">Belum ada alternatif</h5>
                <a href="{{ route('alternatif.create') }}" class="btn btn-primary mt-2">Tambah Alternatif</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kamera</th>
                            <th>Keterangan</th>
                            <th>Nilai Terisi</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($alternatives as $alternative)
                        <tr>
                            <td><span class="code-badge">{{ $alternative->code }}</span></td>
                            <td><strong>{{ $alternative->name }}</strong></td>
                            <td class="text-secondary">{{ $alternative->description ?: '-' }}</td>
                            <td>{{ $alternative->assessments_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('alternatif.edit', $alternative) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('alternatif.destroy', $alternative) }}" class="d-inline" data-confirm="Hapus {{ $alternative->code }} - {{ $alternative->name }}? Nilai terkait ikut terhapus, tetapi snapshot riwayat tetap aman.">
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
