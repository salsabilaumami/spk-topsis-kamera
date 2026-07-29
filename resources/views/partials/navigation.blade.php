<nav class="sidebar-nav nav flex-column">
    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
    <div class="nav-section">Data Master</div>
    <a class="nav-link {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="{{ route('kriteria.index') }}"><i class="bi bi-sliders"></i><span>Kriteria</span></a>
    <a class="nav-link {{ request()->routeIs('alternatif.*') ? 'active' : '' }}" href="{{ route('alternatif.index') }}"><i class="bi bi-camera"></i><span>Alternatif</span></a>
    <a class="nav-link {{ request()->routeIs('assessments.*') ? 'active' : '' }}" href="{{ route('assessments.index') }}"><i class="bi bi-table"></i><span>Input Penilaian</span></a>
    <div class="nav-section">Perhitungan</div>
    <a class="nav-link {{ request()->routeIs('process.*') ? 'active' : '' }}" href="{{ route('process.index') }}"><i class="bi bi-calculator-fill"></i><span>Proses TOPSIS</span></a>
    <a class="nav-link {{ request()->routeIs('results.*') ? 'active' : '' }}" href="{{ route('results.latest') }}"><i class="bi bi-trophy-fill"></i><span>Hasil Ranking</span></a>
    <a class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}" href="{{ route('history.index') }}"><i class="bi bi-clock-history"></i><span>Riwayat</span></a>
    <a class="nav-link {{ request()->routeIs('methodology') ? 'active' : '' }}" href="{{ route('methodology') }}"><i class="bi bi-journal-text"></i><span>Metode TOPSIS</span></a>
</nav>
