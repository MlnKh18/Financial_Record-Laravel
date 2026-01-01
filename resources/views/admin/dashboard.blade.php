@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">

    <h2 class="text-2xl font-bold mb-6">📊 Dashboard Keuangan</h2>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Total Income</p>
            <p class="text-2xl font-bold text-green-600">
                Rp {{ number_format($income) }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Total Expense</p>
            <p class="text-2xl font-bold text-red-600">
                Rp {{ number_format($expense) }}
            </p>
        </div>

        <div class="bg-white rounded-xl shadow p-6 border-l-4 border-blue-500">
            <p class="text-gray-500 text-sm">Balance</p>
            <p class="text-2xl font-bold text-blue-600">
                Rp {{ number_format($balance) }}
            </p>
        </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold mb-4">📅 Income vs Expense</h3>
            <canvas id="monthlyChart"></canvas>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold mb-4">📂 Expense per Category</h3>
            <canvas id="categoryChart"></canvas>
        </div>
    </div>

</div>
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
/**
 * MONTHLY CHART
 */
fetch('/api/admin/reports/charts')
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: data.income,
                    },
                    {
                        label: 'Expense',
                        data: data.expense,
                    }
                ]
            }
        });
    });

/**
 * CATEGORY CHART
 */
fetch('/api/admin/reports/category-chart')
    .then(res => res.json())
    .then(data => {
        new Chart(document.getElementById('categoryChart'), {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.data,
                    backgroundColor: data.colors
                }]
            }
        });
    });
</script>
@endpush
