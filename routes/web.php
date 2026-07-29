<?php

use App\Http\Controllers\AlternativeController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CriterionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\ResultController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::resource('kriteria', CriterionController::class)->parameters(['kriteria' => 'criterion'])->except('show');
    Route::resource('alternatif', AlternativeController::class)->parameters(['alternatif' => 'alternative'])->except('show');

    Route::get('/penilaian', [AssessmentController::class, 'index'])->name('assessments.index');
    Route::put('/penilaian', [AssessmentController::class, 'store'])->name('assessments.store');
    Route::delete('/penilaian/reset', [AssessmentController::class, 'reset'])->name('assessments.reset');

    Route::get('/proses-topsis', [ProcessController::class, 'index'])->name('process.index');
    Route::post('/proses-topsis', [ProcessController::class, 'store'])->name('process.store');

    Route::get('/hasil', [ResultController::class, 'latest'])->name('results.latest');
    Route::get('/hasil/{calculationRun}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/hasil/{calculationRun}/print', [ExportController::class, 'print'])->name('results.print');
    Route::get('/hasil/{calculationRun}/pdf', [ExportController::class, 'pdf'])->name('results.pdf');
    Route::get('/hasil/{calculationRun}/csv', [ExportController::class, 'csv'])->name('results.csv');

    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history.index');
    Route::delete('/riwayat/{calculationRun}', [HistoryController::class, 'destroy'])->name('history.destroy');

    Route::view('/metode-topsis', 'methodology')->name('methodology');
});
