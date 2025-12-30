<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Source;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        Source::create(['name' => 'Cash']);
        Source::create(['name' => 'Bank']);
    }
}
