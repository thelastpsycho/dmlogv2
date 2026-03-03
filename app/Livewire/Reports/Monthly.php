<?php

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Livewire\Component;

class Monthly extends Component
{
    public int $selectedYear;
    public int $selectedMonth;
    public array $availableYears = [];
    public array $availableMonths = [];

    public array $reportData = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;
        $this->selectedMonth = now()->month;

        // Generate available years (current year and 4 years back)
        for ($i = 0; $i < 5; $i++) {
            $year = now()->subYears($i)->year;
            $this->availableYears[$year] = $year;
        }

        // Generate available months
        for ($i = 1; $i <= 12; $i++) {
            $this->availableMonths[$i] = now()->setDateTime($this->selectedYear, $i, 1, 0, 0)->format('F');
        }

        $this->loadReport();
    }

    public function loadReport(): void
    {
        $reportService = app(ReportService::class);
        $this->reportData = $reportService->monthlyReport($this->selectedYear, $this->selectedMonth);
    }

    public function updatedSelectedYear(): void
    {
        $this->loadReport();
    }

    public function updatedSelectedMonth(): void
    {
        $this->loadReport();
    }

    public function render()
    {
        return view('livewire.reports.monthly', [
            'report' => $this->reportData,
            'availableYears' => $this->availableYears,
            'availableMonths' => $this->availableMonths,
        ])->layout('layouts.app')->title('Monthly Report');
    }
}
