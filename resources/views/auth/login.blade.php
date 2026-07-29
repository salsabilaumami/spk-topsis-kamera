@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="login-page">
    <div class="login-wrap">
        <section class="login-visual d-none d-lg-flex">
            <div class="login-overlay"></div>

            <div class="login-copy">
                <div class="login-icon">
                    <i class="bi bi-camera-fill"></i>
                </div>

                <div class="login-eyebrow">
                    Sistem Pendukung Keputusan
                </div>

                <h1>
                    Pilih kamera mirrorless terbaik dengan cepat dan tepat.
                </h1>

                <p class="login-description">
                    Kelola data kamera, kriteria, penilaian, dan hasil
                    perhitungan TOPSIS dalam satu website.
                </p>

                <div class="login-feature-list">
                    <div class="login-feature-item">
                        <div class="login-feature-icon">
                            <i class="bi bi-database-check"></i>
                        </div>

                        <div class="login-feature-content">
                            <h3>Data dinamis</h3>
                            <p>Kelola alternatif dan kriteria sesuai kebutuhan.</p>
                        </div>
                    </div>

                    <div class="login-feature-item">
                        <div class="login-feature-icon">
                            <i class="bi bi-calculator"></i>
                        </div>

                        <div class="login-feature-content">
                            <h3>Perhitungan otomatis</h3>
                            <p>Seluruh tahapan TOPSIS dihitung secara sistematis.</p>
                        </div>
                    </div>

                    <div class="login-feature-item">
                        <div class="login-feature-icon">
                            <i class="bi bi-bar-chart-line"></i>
                        </div>

                        <div class="login-feature-content">
                            <h3>Hasil perangkingan</h3>
                            <p>Rekomendasi kamera terbaik ditampilkan dengan jelas.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="login-panel">
            <div class="login-card">
                <div class="login-card-header">
                    <div class="login-mobile-logo d-lg-none">
                        <i class="bi bi-camera-fill"></i>
                    </div>

                    <div class="login-card-eyebrow">
                        SPK Kamera · Metode TOPSIS
                    </div>

                    <h2>Masuk ke website</h2>

                    <p>
                        Masuk untuk mengelola data kamera dan proses
                        perhitungan TOPSIS.
                    </p>
                </div>

                @include('partials.flash')

                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    class="login-form"
                    autocomplete="off"
                >
                    @csrf

                    <div class="login-form-group">
                        <label for="email" class="form-label">
                            Email
                        </label>

                        <div class="login-field">
                            <span class="login-field-icon">
                                <i class="bi bi-envelope"></i>
                            </span>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Masukkan email"
                                autocomplete="off"
                                required
                                autofocus
                            >
                        </div>

                        @error('email')
                            <div class="login-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="login-form-group">
                        <label for="password" class="form-label">
                            Kata sandi
                        </label>

                        <div class="login-field">
                            <span class="login-field-icon">
                                <i class="bi bi-lock"></i>
                            </span>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Masukkan kata sandi"
                                autocomplete="current-password"
                                required
                            >

                            <button
                                type="button"
                                class="password-toggle"
                                id="passwordToggle"
                                aria-label="Tampilkan kata sandi"
                            >
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>

                        @error('password')
                            <div class="login-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-check login-remember">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            value="1"
                            id="remember"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <label class="form-check-label" for="remember">
                            Ingat saya
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary login-button">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk</span>
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');

    if (!passwordInput || !passwordToggle) {
        return;
    }

    passwordToggle.addEventListener('click', function () {
        const icon = passwordToggle.querySelector('i');
        const isHidden = passwordInput.type === 'password';

        passwordInput.type = isHidden ? 'text' : 'password';

        if (isHidden) {
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
            passwordToggle.setAttribute(
                'aria-label',
                'Sembunyikan kata sandi'
            );
        } else {
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
            passwordToggle.setAttribute(
                'aria-label',
                'Tampilkan kata sandi'
            );
        }
    });
});
</script>
@endpush