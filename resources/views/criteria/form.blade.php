@extends('layouts.app')
@section('title', $criterion->exists ? 'Edit Kriteria' : 'Tambah Kriteria')
@section('page-title', $criterion->exists ? 'Edit Kriteria' : 'Tambah Kriteria')
@section('page-subtitle', 'Kode kriteria dibuat otomatis oleh sistem')
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">{{ $criterion->exists ? $criterion->code.' · '.$criterion->name : 'Kriteria Baru' }}</h5>
                <span class="badge text-bg-light">Total bobot input saat ini: {{ decimal_value($totalWeight, 6) }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $criterion->exists ? route('kriteria.update', $criterion) : route('kriteria.store') }}">
                    @csrf
                    @if($criterion->exists) @method('PUT') @endif

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Nama kriteria <span class="text-danger">*</span></label>
                            <input name="name" class="form-control" value="{{ old('name', $criterion->name) }}" required maxlength="150" placeholder="Contoh: Resolusi efektif">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Satuan</label>
                            <input name="unit" class="form-control" value="{{ old('unit', $criterion->unit) }}" maxlength="100" placeholder="Contoh: megapiksel (MP)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="benefit" @selected(old('type', $criterion->type) === 'benefit')>Benefit — nilai lebih besar lebih baik</option>
                                <option value="cost" @selected(old('type', $criterion->type) === 'cost')>Cost — nilai lebih kecil lebih baik</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Bobot relatif <span class="text-danger">*</span></label>
                            <input name="weight" type="number" step="any" min="0.000000000000001" max="99999.99999" class="form-control" value="{{ old('weight', $criterion->weight) }}" required placeholder="Contoh: 30">
                            <div class="form-text">Bobot tidak wajib berjumlah 1,00. Sistem akan menormalisasikannya otomatis saat proses TOPSIS.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="description" rows="4" class="form-control" maxlength="2000" placeholder="Penjelasan tambahan tentang kriteria">{{ old('description', $criterion->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                        <a href="{{ route('kriteria.index') }}" class="btn btn-light">Batal</a>
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Kriteria</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
