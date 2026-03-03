<?php

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Livewire\Component;

class Yearly extends Component
{
    public int $selectedYear;
    public array $availableYears = [];

    public array $reportData = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;

        // Generate available years (current year and 4 years back)
        for ($i = 0; $i < 5; $i++) {
            $year = now()->subYears($i)->year;
            $this->availableYears[$year] = $year;
        }

        $this->loadReport();
    }

    public function loadReport(): void
    {
        $reportService = app(ReportService::class);
        $this->reportData = $reportService->yearlyReport($this->selectedYear);
    }

    public function updatedSelectedYear(): void
    {
        $this->loadReport();
    }

    public function render()
    {
        return view('livewire.reports.yearly', [
            'report' => $this->reportData,
            'availableYears' => $this->availableYears,
        ])->layout('layouts.app')->title('Yearly Report');
    }
}
