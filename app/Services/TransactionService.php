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

        // 🔐 Role-based visibility
        if (auth()->user()->role !== 'admin') {
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
        $query = Transaction::query()
            ->whereNotNull('approved_at');

        if (!empty($filters['from']) && !empty($filters['to'])) {
            $query->whereBetween('transaction_date', [
                $filters['from'],
                $filters['to']
            ]);
        }

        $income  = (clone $query)->where('type', 'income')->sum('amount');
        $expense = (clone $query)->where('type', 'expense')->sum('amount');

        return [
            'income'  => $income,
            'expense' => $expense,
            'balance' => $income - $expense,
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
