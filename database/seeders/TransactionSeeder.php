<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $category = Category::first();
        $source = Source::first();

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'source_id' => $source->id,
            'type' => 'income',
            'amount' => 500000,
            'transaction_date' => Carbon::now(),
        ]);
    }
}
