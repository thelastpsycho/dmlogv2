<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get monthly report data for bar charts.
     */
    public function month(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'nullable|integer|min:2020|max:2099',
            'month' => 'nullable|integer|min:1|max:12',
        ]);

        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        $report = $this->reportService->monthlyReport($year, $month);

        return response()->json([
            'year' => $report['year'],
            'month' => $report['month'],
            'month_name' => $report['month_name'],
            'total_issues' => $report['total_issues'],
            'by_status' => [
                'open' => $report['by_status']['open'] ?? 0,
                'closed' => $report['by_status']['closed'] ?? 0,
            ],
            'by_department' => $report['by_department']->toArray(),
            'by_issue_type' => $report['by_issue_type']->toArray(),
            'by_severity' => $report['by_severity']->toArray(),
            'avg_close_time_hours' => $report['avg_close_time_hours'],
        ]);
    }

    /**
     * Get yearly report data for bar charts.
     */
    public function year(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'nullable|integer|min:2020|max:2099',
        ]);

        $year = $request->input('year', now()->year);

        $report = $this->reportService->yearlyReport($year);

        // Format monthly data for chart (fill missing months with 0)
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[] = [
                'month' => $i,
                'month_name' => now()->setDateTime($year, $i, 1, 0, 0)->format('M'),
                'count' => $report['by_month'][$i] ?? 0,
            ];
        }

        return response()->json([
            'year' => $report['year'],
            'total_issues' => $report['total_issues'],
            'monthly_data' => $monthlyData,
            'by_status' => [
                'open' => $report['by_status']['open'] ?? 0,
                'closed' => $report['by_status']['closed'] ?? 0,
            ],
            'by_department' => $report['by_department']->toArray(),
            'by_issue_type' => $report['by_issue_type']->toArray(),
            'by_severity' => $report['by_severity']->toArray(),
            'avg_close_time_hours' => $report['avg_close_time_hours'],
        ]);
    }
}
