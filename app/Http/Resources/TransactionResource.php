<?php

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function index()
    {
        return TransactionResource::collection(
            Transaction::with(['category', 'source', 'user'])->latest()->get()
        );
    }
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->transaction_date,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],

            'source' => [
                'id' => $this->source?->id,
                'name' => $this->source?->name,
            ],

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],
        ];
    }
}
