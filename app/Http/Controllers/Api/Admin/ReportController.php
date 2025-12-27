<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

    /**
     * Financial summary report
     * GET /api/admin/reports
     */
    public function index(Request $request)
    {
        $filters = $request->only(['from', 'to']);

        $summary = $this->transactionService->summary($filters);

        return response()->json([
            'period' => $filters,
            'summary' => $summary,
        ]);
    }
    /**
     * Chart-ready financial report
     * GET /api/admin/reports/charts
     */
    public function charts(Request $request)
    {
        $filters = $request->only(['from', 'to', 'type']);

        return response()->json([
            'summary' => $this->transactionService->summary($filters),
            'monthly' => $this->transactionService->monthly($filters),
            'by_category' => $this->transactionService->byCategory($filters),
        ]);
    }
}
