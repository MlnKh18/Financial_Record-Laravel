<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'source_id' => 'required|exists:sources,id',
        'type' => 'required|in:income,expense',
        'amount' => 'required|numeric|min:0',
        'transaction_date' => 'required|date',
    ]);

    $transaction = Transaction::create([
        'user_id' => $request->user()->id,
        'category_id' => $request->category_id,
        'source_id' => $request->source_id,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,
        'transaction_date' => $request->transaction_date,
    ]);

    return response()->json($transaction, 201);
}

}
