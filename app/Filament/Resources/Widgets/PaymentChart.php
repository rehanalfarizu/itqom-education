<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Payment; // Sesuaikan dengan model Payment Anda
use Carbon\Carbon;

class PaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Pembayaran';

    // Menentukan urutan widget di dashboard
    protected static ?int $sort = 2;

    // Ukuran widget (1-12)
    protected int | string | array $columnSpan = 'full';

    // Tinggi widget
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Data untuk 12 bulan terakhir
        $data = [];
        $labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            // Hitung total pembayaran per bulan
            $totalPayments = Payment::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'success') // Sesuaikan dengan status payment Anda
                ->sum('amount'); // Sesuaikan dengan kolom amount Anda

            $data[] = $totalPayments;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pembayaran',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(54, 162, 235, 0.2)',
                    ],
                    'borderColor' => [
                        'rgba(54, 162, 235, 1)',
                    ],
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa diganti: 'bar', 'pie', 'doughnut', 'polarArea', 'radar'
    }

    // Method untuk refresh otomatis (opsional)
    protected static ?string $pollingInterval = '30s';

    // Filter berdasarkan periode (opsional)
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            'week' => 'Minggu Ini',
            'month' => 'Bulan Ini',
            'year' => 'Tahun Ini',
        ];
    }

    // Modifikasi data berdasarkan filter
    protected function getFilter(): array
    {
        $activeFilter = $this->filter;

        switch ($activeFilter) {
            case 'today':
                return $this->getTodayData();
            case 'week':
                return $this->getWeekData();
            case 'month':
                return $this->getMonthData();
            case 'year':
            default:
                return $this->getYearData();
        }
    }

    private function getTodayData(): array
    {
        $hours = [];
        $data = [];

        for ($i = 0; $i < 24; $i++) {
            $hours[] = sprintf('%02d:00', $i);
            $amount = Payment::whereDate('created_at', today())
                ->whereHour('created_at', $i)
                ->where('status', 'success')
                ->sum('amount');
            $data[] = $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran per Jam',
                    'data' => $data,
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $hours,
        ];
    }

    private function getWeekData(): array
    {
        $days = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('D');

            $amount = Payment::whereDate('created_at', $date)
                ->where('status', 'success')
                ->sum('amount');
            $data[] = $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran per Hari',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $days,
        ];
    }

    private function getMonthData(): array
    {
        $weeks = [];
        $data = [];

        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $weeks[] = 'Week ' . ($i + 1);

            $amount = Payment::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->where('status', 'success')
                ->sum('amount');
            $data[] = $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran per Minggu',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
                    'borderColor' => 'rgba(255, 206, 86, 1)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $weeks,
        ];
    }

    private function getYearData(): array
    {
        $months = [];
        $data = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $months[] = $month->format('M Y');

            $amount = Payment::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->where('status', 'success')
                ->sum('amount');
            $data[] = $amount;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembayaran per Bulan',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 2,
                    'fill' => true,
                ],
            ],
            'labels' => $months,
        ];
    }
}
