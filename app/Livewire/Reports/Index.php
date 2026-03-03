<?php

namespace App\Livewire\Reports;

use App\Services\ReportService;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.reports.index')
            ->layout('layouts.app')
            ->title('Reports');
    }
}
