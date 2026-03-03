<?php

namespace App\Livewire\Statistics;

use App\Services\ReportService;
use Livewire\Component;

class Index extends Component
{
    public array $kpiData = [];

    public function mount(): void
    {
        $this->loadKpiData();
    }

    public function loadKpiData(): void
    {
        $reportService = app(ReportService::class);
        $this->kpiData = $reportService->kpiData();
    }

    public function render()
    {
        return view('livewire.statistics.index', [
            'kpi' => $this->kpiData,
        ])->layout('layouts.app')->title('Statistics Dashboard');
    }
}
