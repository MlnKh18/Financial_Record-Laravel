<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
    $income = Transaction::where('type', 'income')->sum('amount');
    $expense = Transaction::where('type', 'expense')->sum('amount');

    return view('admin.dashboard', [
        'income' => $income,
        'expense' => $expense,
        'balance' => $income - $expense,
    ]);
    }
}
