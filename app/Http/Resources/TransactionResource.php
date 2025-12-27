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
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
