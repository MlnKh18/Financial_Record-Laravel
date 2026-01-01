<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransactionService
{
    /**
     * Create new transaction (income / expense)
     */
    public function create(array $data, ?int $userId = null): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {

            return Transaction::create([
                'user_id'          => $userId ?? auth()->id(),
                'category_id'      => $data['category_id'],
                'source_id'        => $data['source_id'],
                'type'             => $data['type'],
                'amount'           => $data['amount'],
                'description'      => $data['description'] ?? null,
                'transaction_date' => $data['transaction_date'],
                'approved_at'      => $this->autoApprove(),
            ]);
        });
    }

    /**
     * List transaction (role aware)
     */
    public function list(array $filters = [])
    {
        $query = Transaction::with(['category', 'source', 'user']);

        if (auth()->check() && auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        // Filters
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        return $query->latest()->paginate(10);
    }

    /**
     * Financial summary (approved only)
     */
    public function summary(array $filters = []): array
    {
        $query = Transaction::query();

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        $income  = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return [
            'income'  => (float) $income,
            'expense' => (float) $expense,
            'balance' => (float) ($income - $expense),
        ];
    }
    /**
     * Monthly financial report
     */
    public function monthly(array $filters = [])
    {
        $query = DB::table('transactions')
            ->selectRaw('
            DATE_FORMAT(transaction_date, "%Y-%m") as month,
            SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense
        ')
            ->whereNotNull('approved_at');

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        return $query
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'month'   => $row->month,
                'income'  => (float) $row->income,
                'expense' => (float) $row->expense,
                'balance' => (float) $row->income - $row->expense,
            ]);
    }

    /**
     * Category based report
     */
    public function byCategory(array $filters = [])
    {
        $query = DB::table('transactions')
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->selectRaw('
            categories.name as category,
            SUM(transactions.amount) as total
        ')
            ->whereNotNull('transactions.approved_at');

        if (!empty($filters['type'])) {
            $query->where('transactions.type', $filters['type']);
        }

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        return $query
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn($row) => [
                'category' => $row->category,
                'total' => (float) $row->total,
            ]);
    }

    public function chartByCategory(): array
    {
        $data = Transaction::with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get();

        return [
            'labels' => $data->pluck('category.name'),
            'values' => $data->pluck('total'),
        ];
    }


    /**
     * Approve transaction (ADMIN)
     */
    public function approve(Transaction $transaction): Transaction
    {
        $transaction->update([
            'approved_at' => Carbon::now(),
        ]);

        return $transaction;
    }

    public function report(array $filters = []): array
    {
        $query = Transaction::with(['category', 'source']);

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        $transactions = $query->latest()->get();

        // Summary
        $income = $transactions->where('type', 'income')->sum('amount');
        $expense = $transactions->where('type', 'expense')->sum('amount');

        // Monthly grouping
        $monthly = $transactions
            ->groupBy(fn($t) => $t->transaction_date->format('Y-m'))
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'income' => $items->where('type', 'income')->sum('amount'),
                    'expense' => $items->where('type', 'expense')->sum('amount'),
                ];
            })->values();

        // Category grouping
        $byCategory = $transactions
            ->groupBy(fn($t) => $t->category->name)
            ->map(function ($items, $category) {
                return [
                    'category' => $category,
                    'total' => $items->sum('amount'),
                ];
            })->values();

        return [
            'summary' => [
                'income' => $income,
                'expense' => $expense,
                'balance' => $income - $expense,
            ],
            'transactions' => $transactions->map(function ($t) {
                return [
                    'id' => $t->id,
                    'type' => $t->type,
                    'amount' => $t->amount,
                    'category' => $t->category->name,
                    'source' => $t->source->name,
                    'date' => $t->transaction_date->toDateString(),
                ];
            }),
            'grouping' => [
                'monthly' => $monthly,
                'by_category' => $byCategory,
            ]
        ];
    }

    public function chartMonthly(array $filters = []): array
    {
        $query = Transaction::query();

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        $data = $query
            ->selectRaw("
            DATE_FORMAT(transaction_date, '%Y-%m') as month,
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
        ")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'labels' => $data->pluck('month'),
            'income' => $data->pluck('income'),
            'expense' => $data->pluck('expense'),
        ];
    }

    /**
     * Auto approve admin transaction
     */
    protected function autoApprove(): ?Carbon
    {
        return auth()->user()->role === 'admin'
            ? Carbon::now()
            : null;
    }
}
