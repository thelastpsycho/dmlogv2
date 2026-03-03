<?php

namespace App\Services;

use App\Models\Issue;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ExportService
{
    /**
     * Export a single issue to PDF.
     */
    public function exportIssue(Issue $issue)
    {
        $issue->load(['departments', 'issueTypes', 'createdBy', 'updatedBy', 'closedBy', 'assignedTo', 'comments']);

        $pdf = PDF::loadView('exports.issue', [
            'issue' => $issue,
        ]);

        $filename = 'issue-' . $issue->id . '-' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export logbook to PDF.
     */
    public function exportLogbook(Collection $issues, array $filters = [])
    {
        $pdf = PDF::loadView('exports.logbook', [
            'issues' => $issues,
            'filters' => $filters,
        ]);

        $dateRange = '';
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $dateRange = $filters['date_from'] . '-to-' . $filters['date_to'];
        } else {
            $dateRange = now()->format('Ymd');
        }

        $filename = 'logbook-' . $dateRange . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export monthly report to PDF.
     */
    public function exportMonthlyReport(array $reportData)
    {
        $pdf = PDF::loadView('exports.monthly-report', [
            'report' => $reportData,
        ]);

        $filename = 'monthly-report-' . $reportData['year'] . '-' . str_pad($reportData['month'], 2, '0', STR_PAD_LEFT) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export yearly report to PDF.
     */
    public function exportYearlyReport(array $reportData)
    {
        $pdf = PDF::loadView('exports.yearly-report', [
            'report' => $reportData,
        ]);

        $filename = 'yearly-report-' . $reportData['year'] . '.pdf';

        return $pdf->download($filename);
    }
}
