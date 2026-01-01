<?php

namespace App\Http\Controllers\Api\Admin;

use App\Exports\TransactionsExport;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
    public function categoryChart()
    {
        return response()->json(
            $this->transactionService->chartByCategory()
        );
    }
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new TransactionsExport($request->all()),
            'laporan-keuangan.xlsx'
        );
    }
    public function exportPdf(Request $request)
    {
        $transactions = $this->transactionService
            ->list($request->all())
            ->getCollection();

        $pdf = Pdf::loadView('reports.transactions-pdf', [
            'transactions' => $transactions,
        ]);

        return $pdf->download('laporan-keuangan.pdf');
    }
}
