<?php

namespace App\Livewire\Reports;

use App\Models\Department;
use App\Models\IssueType;
use App\Services\ReportService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Logbook extends Component
{
    public ?string $dateFrom = null;
    public ?string $dateTo = null;
    public ?int $departmentId = null;
    public ?int $issueTypeId = null;
    public ?string $status = null;

    public Collection $issues;
    public Collection $departments;
    public Collection $issueTypes;

    protected ReportService $reportService;

    public function mount(): void
    {
        $this->reportService = app(ReportService::class);

        // Set default date range (current month)
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');

        $this->departments = Department::orderBy('name')->get();
        $this->issueTypes = IssueType::orderBy('name')->get();

        $this->loadReport();
    }

    public function loadReport(): void
    {
        $filters = [];

        if ($this->dateFrom) {
            $filters['date_from'] = $this->dateFrom;
        }
        if ($this->dateTo) {
            $filters['date_to'] = $this->dateTo;
        }
        if ($this->departmentId) {
            $filters['department_id'] = $this->departmentId;
        }
        if ($this->issueTypeId) {
            $filters['issue_type_id'] = $this->issueTypeId;
        }
        if ($this->status) {
            $filters['status'] = $this->status;
        }

        $this->issues = $this->reportService->logbookReport($filters);
    }

    public function updatedDateFrom(): void
    {
        $this->loadReport();
    }

    public function updatedDateTo(): void
    {
        $this->loadReport();
    }

    public function updatedDepartmentId(): void
    {
        $this->loadReport();
    }

    public function updatedIssueTypeId(): void
    {
        $this->loadReport();
    }

    public function updatedStatus(): void
    {
        $this->loadReport();
    }

    public function clearFilters(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
        $this->departmentId = null;
        $this->issueTypeId = null;
        $this->status = null;
        $this->loadReport();
    }

    public function render()
    {
        return view('livewire.reports.logbook', [
            'issues' => $this->issues,
            'departments' => $this->departments,
            'issueTypes' => $this->issueTypes,
        ])->layout('layouts.app')->title('Logbook Report');
    }
}
