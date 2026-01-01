<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    /**
     * GET /api/transactions
     */
    public function index(Request $request)
    {
        return response()->json(
            $this->transactionService->list($request->all())
        );
    }

    /**
     * POST /api/transactions
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'source_id'        => 'required|exists:sources,id',
            'type'             => 'required|in:income,expense',
            'amount'           => 'required|numeric|min:0',
            'description'      => 'nullable|string',
            'transaction_date' => 'required|date',
        ]);

        $transaction = $this->transactionService->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return response()->json($transaction, 201);
    }

}
