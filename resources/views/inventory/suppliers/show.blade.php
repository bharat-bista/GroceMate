@extends('inventory.layouts.inventory')

@section('title','Supplier Details')
@section('heading','Supplier Details')
@section('subtitle','View supplier information and transaction history')

@section('content')
<div class="space-y-6">
    <!-- Supplier Information Card -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-semibold text-slate-900">{{ $supplier->name }}</h3>
                <p class="text-sm text-slate-500">Supplier ID: {{ $supplier->id }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('inventory.suppliers.edit', $supplier) }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                    Edit Supplier
                </a>
                <a href="{{ route('inventory.suppliers.index') }}" data-back-button
                   class="px-4 py-2 bg-white border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 transition duration-200">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="text-sm text-slate-500">Supplier Type</label>
                <p class="font-medium capitalize">{{ $supplier->supplier_type }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Phone</label>
                <p class="font-medium">{{ $supplier->phone ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Email</label>
                <p class="font-medium">{{ $supplier->email ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">VAT Number</label>
                <p class="font-medium">{{ $supplier->vat_number ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">PAN Number</label>
                <p class="font-medium">{{ $supplier->pan_number ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Business Account</label>
                <p class="font-medium">{{ $supplier->businessAccount->business_name ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm text-slate-500">Total Due</label>
                <p class="font-semibold text-red-600">
                    Rs {{ number_format($supplier->calculated_total_due, 2) }}
                </p>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <label class="text-sm text-slate-500">Address</label>
                <p class="font-medium">{{ $supplier->address ?? '-' }}</p>
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
        <option value="monthly">Monthly</option>
        <option value="yearly">Yearly</option>
      </select>
    </div>
  </div>

  <div style="height: 300px; position: relative;">
    <canvas id="transactionChart"></canvas>
  </div>
</div>

    <!-- Purchase and Payment Records Side by Side -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Purchase Records Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Purchase Records</h3>
                <a href="{{ route('inventory.purchases.create') }}?supplier_id={{ $supplier->id }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                    + Add Purchase
                </a>
            </div>

            @if($purchases->count() > 0)
                <div class="text-xs text-slate-500 mb-2">
                    Showing {{ $purchases->firstItem() }} to {{ $purchases->lastItem() }} of {{ $purchases->total() }} results
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
                            @foreach($purchases as $index => $purchase)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-500">{{ ($purchases->currentPage() - 1) * $purchases->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $purchase->invoice_no ?? 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ date('M d, Y', strtotime($purchase->created_at)) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-blue-600 font-semibold">
                                            Rs {{ number_format($purchase->total_cost, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-700">
                                            Pending
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($purchases->hasPages())
                    <div class="mt-4">
                        {{ $purchases->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No purchase records found for this supplier.</p>
                    <a href="{{ route('inventory.purchases.create') }}?supplier_id={{ $supplier->id }}" 
                       class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition duration-200">
                        Add First Purchase
                    </a>
                </div>
            @endif
        </div>

        <!-- Payment Records Section -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Payment Records</h3>
                <a href="{{ route('pos.supplier-payments.create') }}?supplier_id={{ $supplier->id }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition duration-200">
                    + Add Payment
                </a>
            </div>

            @if($payments->count() > 0)
                <div class="text-xs text-slate-500 mb-2">
                    Showing {{ $payments->firstItem() }} to {{ $payments->lastItem() }} of {{ $payments->total() }} results
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-slate-500 bg-slate-50">
                            <tr>
                                <th class="text-left px-4 py-3">#</th>
                                <th class="text-left px-4 py-3">Reference</th>
                                <th class="text-left px-4 py-3">Date</th>
                                <th class="text-left px-4 py-3">Amount</th>
                                <th class="text-left px-4 py-3">Method</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($payments as $index => $payment)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-slate-500">{{ ($payments->currentPage() - 1) * $payments->perPage() + $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ $payment->payment_reference ?? 'PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ date('M d, Y', strtotime($payment->date)) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-green-600 font-semibold">
                                            Rs {{ number_format($payment->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 capitalize">
                                            {{ $payment->payment_method ?? 'cash' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($payments->hasPages())
                    <div class="mt-4">
                        {{ $payments->links('pagination::tailwind') }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <p class="text-slate-500">No payment records found for this supplier.</p>
                    <a href="{{ route('pos.supplier-payments.create') }}?supplier_id={{ $supplier->id }}" 
                       class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition duration-200">
                        Add First Payment
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Ledger Section -->
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-slate-900">Ledger Transactions</h3>
            <div class="text-sm text-slate-500">
                Complete transaction history
            </div>
        </div>

        @if($ledgerTransactions->count() > 0)
            <div class="text-xs text-slate-500 mb-2">
                Showing {{ $ledgerTransactions->firstItem() }} to {{ $ledgerTransactions->lastItem() }} of {{ $ledgerTransactions->total() }} results
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-slate-500 bg-slate-50">
                        <tr>
                            <th class="text-left px-4 py-3">Date</th>
                            <th class="text-left px-4 py-3">Reference</th>
                            <th class="text-left px-4 py-3">Description</th>
                            <th class="text-right px-4 py-3">Debit</th>
                            <th class="text-right px-4 py-3">Credit</th>
                            <th class="text-right px-4 py-3">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($ledgerTransactions as $transaction)
                            <tr>
                                <td class="px-4 py-3">{{ date('M d, Y', strtotime($transaction['date'])) }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-medium">{{ $transaction['reference'] }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $transaction['description'] }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($transaction['debit'] > 0)
                                        <span class="text-green-600 font-semibold">
                                            Rs {{ number_format($transaction['debit'], 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($transaction['credit'] > 0)
                                        <span class="text-red-600 font-semibold">
                                            Rs {{ number_format($transaction['credit'], 2) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="font-semibold
                                        @if($transaction['balance'] > 0) text-red-600
                                        @elseif($transaction['balance'] < 0) text-green-600
                                        @else text-slate-600
                                        @endif">
                                        Rs {{ number_format(abs($transaction['balance']), 2) }}
                                        @if($transaction['balance'] > 0) (Cr)
                                        @elseif($transaction['balance'] < 0) (Dr)
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($ledgerTransactions->hasPages())
                <div class="mt-4">
                    {{ $ledgerTransactions->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="text-center py-8">
                <p class="text-slate-500">No ledger transactions found for this supplier.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
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
                        label: 'Payments',
                        data: window.transactionChartData.monthly.payments,
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Purchases',
                        data: window.transactionChartData.monthly.purchases,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rs ' + context.parsed.y.toLocaleString('en-IN', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs ' + value.toLocaleString('en-IN');
                            }
                        }
                    }
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
                transactionChart.data.datasets[0].data = data.payments;
                transactionChart.data.datasets[1].data = data.purchases;
                transactionChart.update();
                console.log('Chart updated successfully');
            } else {
                console.log('No data found for mode:', mode);
            }
        };
    }
});
</script>
@endpush
