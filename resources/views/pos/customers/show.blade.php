@extends('inventory.layouts.inventory')

@section('title','Customer Details')
@section('heading','Customer Details')
@section('subtitle','View customer information and transaction history')

@section('content')
<div class="space-y-6">
    <!-- Customer Information Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">{{ $customer->name }}</h3>
                <p class="text-sm text-slate-500">Customer ID: {{ $customer->id }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('pos.customers.edit', $customer) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                    Edit Customer
                </a>
                <a href="{{ route('pos.customers.index') }}" data-back-button
                   class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="text-sm text-slate-500">Customer Type</label>
                <p class="font-medium capitalize">{{ $customer->customer_type }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Phone</label>
                <p class="font-medium">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Email</label>
                <p class="font-medium">{{ $customer->email ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">VAT Number</label>
                <p class="font-medium">{{ $customer->vat_number ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">PAN Number</label>
                <p class="font-medium">{{ $customer->pan_number ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Total Due</label>
                <p class="font-medium @if($customer->calculated_total_due > 0) text-red-600 @else text-emerald-600 @endif">
                    Rs {{ number_format($customer->calculated_total_due, 2) }}
                </p>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="text-sm text-slate-500">Address</label>
                <p class="font-medium">{{ $customer->address ?? '-' }}</p>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="text-sm text-slate-500">Notes</label>
                <p class="font-medium">{{ $customer->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

<!-- Transaction Chart Section -->
<div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-5" x-data="{
  chartMode: 'monthly'
}">
  <div class="flex items-center justify-between mb-4">
    <div class="font-semibold">Transaction Overview</div>
    
    <div class="flex items-center gap-2">
      <select x-model="chartMode" @change="window.updateChartMode($event.target.value)"
              class="rounded-lg border-slate-300 text-sm px-3 py-2">
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly" selected>Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>
  </div>

  <div style="height: 300px; position: relative;">
    <canvas id="transactionChart"></canvas>
  </div>
</div>

<!-- Customer Ledger Section -->
<div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
    <div class="flex items-center justify-between mb-6">
        <div class="font-semibold text-lg">Customer Ledger</div>
        <div class="flex items-center gap-4">
            <div class="text-sm">
                <span class="text-slate-500">Opening Balance:</span>
                <span class="font-semibold ml-2">
                    Rs {{ number_format($customer->opening_due ?? 0, 2) }}
                </span>
            </div>
            <div class="text-sm">
                <span class="text-slate-500">Current Balance:</span>
                <span class="font-semibold ml-2 {{ $customer->calculated_total_due > 0 ? 'text-red-600' : 'text-green-600' }}">
                    Rs {{ number_format($customer->calculated_total_due, 2) }}
                </span>
            </div>
        </div>
    </div>

    @if($ledgerTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-slate-500 bg-slate-50">
                    <tr>
                        <th class="text-left px-4 py-3">Date</th>
                        <th class="text-left px-4 py-3">Reference</th>
                        <th class="text-left px-4 py-3">Description</th>
                        <th class="text-right px-4 py-3">Dr. Amount</th>
                        <th class="text-right px-4 py-3">Cr. Amount</th>
                        <th class="text-right px-4 py-3">Balance Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($ledgerTransactions as $index => $transaction)
                        <tr class="
    @if($transaction['type'] == 'sale') bg-red-50
    @elseif($transaction['type'] == 'income') bg-green-50
    @else bg-gray-100
    @endif
">
                            <td class="px-4 py-3">
                                {{ date('M d, Y', strtotime($transaction['date'])) }}
                            </td>
                            <td class="px-4 py-3 font-medium">
                                {{ $transaction['reference'] }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $transaction['description'] }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($transaction['debit'] > 0)
                                    <span class="text-red-600 font-semibold">
                                        Dr {{ number_format($transaction['debit'], 2) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($transaction['credit'] > 0)
                                    <span class="text-green-600 font-semibold">
                                        Cr {{ number_format($transaction['credit'], 2) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">
                                @if($transaction['balance'] > 0)
                                    <span class="text-red-600">
                                        Cr {{ number_format($transaction['balance'], 2) }}
                                    </span>
                                @elseif($transaction['balance'] < 0)
                                    <span class="text-green-600">
                                        Dr {{ number_format(abs($transaction['balance']), 2) }}
                                    </span>
                                @else
                                    <span class="text-slate-600">0.00</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination for Ledger -->
        @if($ledgerTransactions->count() > 0)
            <div class="mt-4">
                <div class="text-xs text-slate-500 mb-2">
                    Showing {{ $ledgerTransactions->firstItem() }} to {{ $ledgerTransactions->lastItem() }} of {{ $ledgerTransactions->total() }} transactions
                </div>
                <div class="flex gap-2">
                    <div id="ledger-pagination-container">
                        {{ $ledgerTransactions->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="text-center py-8">
            <p class="text-slate-500">No transactions found for this customer.</p>
        </div>
    @endif
</div>

<!-- Transaction Records Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Sales Records Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            @php
                $sales = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                    ->orderBy('created_at', 'desc')
                    ->paginate(5, ['*'], 'sales_page');
            @endphp

            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Sales Records</h3>
                <a href="{{ route('pos.invoices.create') }}?customer_id={{ $customer->id }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                    + Add Sale
                </a>
            </div>

            @if($sales->count() > 0)
                <div class="text-xs text-slate-500 mb-2">
                    Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} results
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-500 bg-slate-50">
                            <tr>
                                <th class="text-left px-4 py-3">#</th>
                                <th class="text-left px-4 py-3">Invoice No</th>
                                <th class="text-left px-4 py-3">Date</th>
                                <th class="text-left px-4 py-3">Amount</th>
                                <th class="text-left px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($sales as $index => $sale)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-500">{{ ($sales->currentPage() - 1) * $sales->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $sale->invoice_no }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ date('M d, Y', strtotime($sale->created_at)) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-blue-600 font-semibold">
                                            Rs {{ number_format($sale->total_cost, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($sale->status === 'Complete')
                                            <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                               Due Paid
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($sales->hasPages())
                    <div class="mt-4">
                        {{ $sales->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No sales records found for this customer.</p>
                    <a href="{{ route('pos.invoices.create') }}?customer_id={{ $customer->id }}" 
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                        Add First Sale
                    </a>
                </div>
            @endif
        </div>

        <!-- Income Records Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Income Records</h3>
                <a href="{{ route('pos.income.create') }}?customer_id={{ $customer->id }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition duration-200">
                    + Add Income
                </a>
            </div>

            @if($incomes->count() > 0)
                <div class="text-xs text-slate-500 mb-2">
                    Showing {{ $incomes->firstItem() }} to {{ $incomes->lastItem() }} of {{ $incomes->total() }} results
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-500 bg-slate-50">
                            <tr>
                                <th class="text-left px-4 py-3">#</th>
                                <th class="text-left px-4 py-3">Reference</th>
                                <th class="text-left px-4 py-3">Date</th>
                                <th class="text-left px-4 py-3">Amount</th>
                                <th class="text-left px-4 py-3">Type</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($incomes as $index => $income)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-500">{{ ($incomes->currentPage() - 1) * $incomes->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ date('M d, Y', strtotime($income->transaction_date)) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-emerald-600 font-semibold">
                                            Rs {{ number_format($income->amount_received, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($income->income_type == 'Sale') bg-emerald-100 text-emerald-700
                                            @elseif($income->income_type == 'Due Collection') bg-amber-100 text-amber-700
                                            @else bg-slate-100 text-slate-700
                                            @endif">
                                            {{ $income->income_type }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($incomes->hasPages())
                    <div class="mt-4">
                        {{ $incomes->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No income records found for this customer.</p>
                    <a href="{{ route('pos.income.create') }}?customer_id={{ $customer->id }}" 
                       class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition duration-200">
                        Add First Income Record
                    </a>
                </div>
            @endif
        </div>
    </div>
    
</div>

@if(session('success'))
<div class="mt-4 p-4 rounded-xl bg-green-100 text-green-700 border border-green-200 shadow-sm">
  {{ session('success') }}
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
// Make chart data available globally
window.transactionChartData = @json($chartData);

// Initialize chart when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('transactionChart');
    if (ctx) {
        let transactionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.transactionChartData.monthly.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: window.transactionChartData.monthly.income,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.3,
                        fill: false,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Sales',
                        data: window.transactionChartData.monthly.sales,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: false,
                        borderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            boxWidth: 8,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#ddd',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += 'Rs ' + context.parsed.y.toFixed(2);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                if (value >= 1000) {
                                    return 'Rs ' + (value / 1000) + 'k';
                                }
                                return 'Rs ' + value;
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Function to update chart data based on mode
        window.updateChartMode = function(mode) {
            console.log('Updating chart to mode:', mode);
            const data = window.transactionChartData[mode];
            if (data) {
                console.log('Data found for mode:', mode, data);
                transactionChart.data.labels = data.labels;
                transactionChart.data.datasets[0].data = data.income;
                transactionChart.data.datasets[1].data = data.sales;
                transactionChart.update();
                console.log('Chart updated successfully');
            } else {
                console.log('No data found for mode:', mode);
            }
        };
    }
});

// Handle ledger pagination with AJAX
document.addEventListener('DOMContentLoaded', function() {
    const ledgerContainer = document.getElementById('ledger-pagination-container');
    if (ledgerContainer) {
        ledgerContainer.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.getAttribute('href')) {
                e.preventDefault();
                
                const page = new URL(link.getAttribute('href')).searchParams.get('ledger_page');
                const currentPage = {{ $ledgerTransactions->currentPage() }};
                
                if (page && page != currentPage) {
                    // Show loading state
                    ledgerContainer.innerHTML = '<div class="text-center py-4"><p class="text-slate-500">Loading...</p></div>';
                    
                    // Load new page content with AJAX
                    fetch(link.getAttribute('href'), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Create temporary div to render pagination
                            const tempDiv = document.createElement('div');
                            tempDiv.innerHTML = data.pagination;
                            
                            // Update the showing info
                            const infoDiv = ledgerContainer.closest('.mt-4').querySelector('.text-xs');
                            if (infoDiv) {
                                const newPage = new URL(link.getAttribute('href')).searchParams.get('ledger_page');
                                infoDiv.textContent = `Showing ${data.firstItem} to ${data.lastItem} of ${data.total} transactions`;
                            }
                        })
                        .catch(error => {
                            console.error('Error loading page:', error);
                            ledgerContainer.innerHTML = '<div class="text-center py-4"><p class="text-red-500">Error loading page</p></div>';
                        });
                }
            }
        });
    }
});
</script>
@endsection
