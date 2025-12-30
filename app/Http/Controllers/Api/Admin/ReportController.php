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
        return response()->json(
            $this->transactionService->report($request->all())
        );
    }
    /**
     * Chart-ready financial report
     * GET /api/admin/reports/charts
     */
    public function chart(Request $request)
    {
        return response()->json(
            $this->transactionService->chartMonthly($request->all())
        );
    }
    public function categoryChart(){
        return response()->json(
            $this->transactionService->chartByCategory()
        );
    }
}
