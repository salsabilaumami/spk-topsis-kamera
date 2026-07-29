<?php

namespace App\Http\Controllers;

use App\Models\CalculationRun;
use App\Support\Number;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function print(CalculationRun $calculationRun): Response
    {
        $run = $this->load($calculationRun);
        return response()->view('results.report', ['run' => $run, 'forPdf' => false]);
    }

    public function pdf(CalculationRun $calculationRun): Response
    {
        $run = $this->load($calculationRun);
        return Pdf::loadView('results.report', ['run' => $run, 'forPdf' => true])
            ->setPaper('a4', 'portrait')
            ->download('hasil-'.$run->code.'.pdf');
    }

    public function csv(CalculationRun $calculationRun): StreamedResponse
    {
        $run = $this->load($calculationRun);
        return response()->streamDownload(function () use ($run): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Ranking', 'Kode', 'Nama Kamera', 'D+', 'D-', 'Nilai Preferensi', 'Status'], ';', '"', '');
            foreach ($run->alternatives as $alternative) {
                fputcsv($handle, [
                    $alternative->rank,
                    $alternative->code,
                    $alternative->name,
                    Number::decimal($alternative->d_positive),
                    Number::decimal($alternative->d_negative),
                    Number::decimal($alternative->preference),
                    $alternative->recommendation_status,
                ], ';', '"', '');
            }
            fclose($handle);
        }, 'hasil-'.$run->code.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function load(CalculationRun $run): CalculationRun
    {
        abort_unless($run->status === 'completed', 404);
        return $run->load(['criteria', 'alternatives' => fn ($query) => $query->orderBy('rank')]);
    }
}
