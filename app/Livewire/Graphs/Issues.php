<?php

namespace App\Livewire\Graphs;

use App\Models\Issue;
use Livewire\Component;

class Issues extends Component
{
    public int $selectedYear;
    public array $availableYears = [];
    public array $chartData = [];

    public function mount(): void
    {
        $this->selectedYear = now()->year;

        // Generate available years (current year and 4 years back)
        for ($i = 0; $i < 5; $i++) {
            $year = now()->subYears($i)->year;
            $this->availableYears[$year] = $year;
        }

        $this->loadChartData();
    }

    public function loadChartData(): void
    {
        // Monthly trend data
        $monthlyData = Issue::whereYear('created_at', $this->selectedYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Fill missing months with 0
        for ($i = 1; $i <= 12; $i++) {
            $this->chartData['monthly'][] = [
                'month' => $i,
                'label' => now()->setDateTime($this->selectedYear, $i, 1, 0, 0)->format('M'),
                'count' => $monthlyData[$i] ?? 0,
            ];
        }

        // Status breakdown
        $statusData = Issue::whereYear('created_at', $this->selectedYear)
            ->selectRaw('
                CASE
                    WHEN closed_at IS NOT NULL THEN "closed"
                    ELSE "open"
                END as status,
                COUNT(*) as count
            ')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->chartData['status'] = [
            'open' => $statusData['open'] ?? 0,
            'closed' => $statusData['closed'] ?? 0,
        ];

        // Department breakdown
        $deptData = Issue::whereYear('created_at', $this->selectedYear)
            ->selectRaw('d.name, COUNT(*) as count')
            ->join('department_issue as di', 'issues.id', '=', 'di.issue_id')
            ->join('departments as d', 'di.department_id', '=', 'd.id')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $this->chartData['departments'] = $deptData->map(fn($item) => [
            'name' => $item->name,
            'count' => $item->count,
        ])->toArray();

        // Issue type breakdown
        $typeData = Issue::whereYear('created_at', $this->selectedYear)
            ->selectRaw('it.name, COUNT(*) as count')
            ->join('issue_issue_type as iit', 'issues.id', '=', 'iit.issue_id')
            ->join('issue_types as it', 'iit.issue_type_id', '=', 'it.id')
            ->groupBy('it.id', 'it.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $this->chartData['issueTypes'] = $typeData->map(fn($item) => [
            'name' => $item->name,
            'count' => $item->count,
        ])->toArray();
    }

    public function updatedSelectedYear(): void
    {
        $this->loadChartData();
    }

    public function render()
    {
        return view('livewire.graphs.issues', [
            'chartData' => $this->chartData,
            'availableYears' => $this->availableYears,
        ])->layout('layouts.app')->title('Issues Graphs');
    }
}
