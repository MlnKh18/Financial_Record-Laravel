<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; }
    </style>
</head>
<body>

<h3>Laporan Transaksi</h3>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Sumber</th>
            <th>Tipe</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $t)
        <tr>
            <td>{{ $t->transaction_date }}</td>
            <td>{{ $t->category->name ?? '-' }}</td>
            <td>{{ $t->source->name ?? '-' }}</td>
            <td>{{ $t->type }}</td>
            <td>{{ number_format($t->amount, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
