<?php

namespace App\Exports;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TransactionsExport implements FromCollection, WithHeadings
{
    public function __construct(private array $filters = []) {}

    public function collection(): Collection
    {
        $query = Transaction::with(['category', 'source', 'user']);

        if (!empty($this->filters['from']) && !empty($this->filters['to'])) {
            $query->whereBetween('transaction_date', [
                $this->filters['from'],
                $this->filters['to']
            ]);
        }

        return $query->get()->map(function ($t) {
            return [
                'Tanggal'     => $t->transaction_date->format('Y-m-d'),
                'Tipe'        => ucfirst($t->type),
                'Kategori'    => $t->category->name,
                'Sumber'      => $t->source->name,
                'Jumlah'      => $t->amount,
                'Deskripsi'   => $t->description,
                'Dibuat oleh' => $t->user->name,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Tipe',
            'Kategori',
            'Sumber',
            'Jumlah',
            'Deskripsi',
            'Dibuat oleh'
        ];
    }
}
