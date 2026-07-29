@extends('layouts.app')
@section('title', $alternative->exists ? 'Edit Alternatif' : 'Tambah Alternatif')
@section('page-title', $alternative->exists ? 'Edit Alternatif' : 'Tambah Alternatif')
@section('page-subtitle', 'Masukkan identitas kamera mirrorless yang akan dinilai')
@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 fw-bold">{{ $alternative->exists ? $alternative->code.' · '.$alternative->name : 'Alternatif Baru' }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $alternative->exists ? route('alternatif.update', $alternative) : route('alternatif.store') }}">
                    @csrf
                    @if($alternative->exists) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama kamera mirrorless <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" value="{{ old('name', $alternative->name) }}" required maxlength="150" placeholder="Contoh: Canon EOS R50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="description" class="form-control" rows="5" maxlength="3000" placeholder="Deskripsi singkat kamera">{{ old('description', $alternative->description) }}</textarea>
                    </div>
                    <div class="alert alert-light border">
                        <i class="bi bi-info-circle me-1"></i>
                        Kode alternatif akan dibuat otomatis mengikuti ID data, misalnya A1, A2, A3, dan seterusnya.
                    </div>
                    <div class="d-flex flex-column flex-sm-row justify-content-end gap-2">
                        <a href="{{ route('alternatif.index') }}" class="btn btn-light">Batal</a>
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Alternatif</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
