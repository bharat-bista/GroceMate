@extends('inventory.layouts.inventory')


@section('title','Inventory Dashboard')
@section('heading','Dashboard')
@section('subtitle','Overview of products & alerts')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Total Products</div>
    <div class="text-3xl font-bold mt-2">{{ $totalProducts }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Active Products</div>
    <div class="text-3xl font-bold mt-2">{{ $activeProducts }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Low Stock</div>
    <div class="text-3xl font-bold mt-2">{{ $lowStockCount }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Total Supplier</div>
    <div class="text-3xl font-bold mt-2">{{ $supplierCount }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Expiring Soon</div>
    <div class="text-3xl font-bold mt-2">{{ $expiringSoonCount }}</div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
    <div class="text-sm text-slate-500">Expired Product</div>
    <div class="text-3xl font-bold mt-2">{{ $expiredCount }}</div>
  </div>
</div>
<div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
  <div class="flex items-center justify-between mb-4">
    <div class="font-semibold">Purchase Overview</div>

    <select id="chartMode"
            class="rounded-lg border-slate-300 text-sm px-3 py-2">
      <option value="daily">Daily</option>
      <option value="weekly">Weekly</option>
      <option value="monthly" selected>Monthly</option>
      <option value="yearly">Yearly</option>
    </select>
  </div>

  <canvas id="purchaseChart" height="120"></canvas>
</div>
<div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
  <div class="p-5 border-b border-slate-200 flex items-center justify-between">
    <div class="font-semibold">Low Stock Items</div>
    <a class="text-sm text-slate-700 underline" href="{{ route('inventory.products.index') }}">View Products</a>
  </div>
  <div class="p-5 overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-slate-500">
        <tr>
          <th class="text-left py-2">Product</th>
          <th class="text-left py-2">Qty</th>
          <th class="text-left py-2">Reorder Level</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($topLowStock as $row)
          <tr>
            <td class="py-3">{{ $row->product->name ?? '—' }}</td>
            <td class="py-3 font-semibold">{{ $row->quantity }}</td>
            <td class="py-3">{{ $row->reorder_level }}</td>
          </tr>
        @empty
          <tr><td class="py-3 text-slate-500" colspan="3">No low stock items.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
const chartData = {
  daily: {
    labels: @json($dailyPurchases->map(fn($p) => \Carbon\Carbon::parse($p->label)->format('M d'))->values()->all()),
    data: @json($dailyPurchases->pluck('total')->values()->all()),
  },
  weekly: {
    labels: @json($weeklyPurchases->map(fn($p) => "Week " . substr($p->label, -2))->values()->all()), 
    data: @json($weeklyPurchases->pluck('total')->values()->all()),
  },
  monthly: {
    labels: @json($monthlyPurchases->map(fn($p) => \Carbon\Carbon::parse($p->label . "-01")->format('M Y'))->values()->all()),
    data: @json($monthlyPurchases->pluck('total')->values()->all()),
  },
  yearly: {
    labels: @json($yearlyPurchases->pluck('label')->values()->all()),
    data: @json($yearlyPurchases->pluck('total')->values()->all()),
  },
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('purchaseChart').getContext('2d');

let purchaseChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: chartData.monthly.labels,
    datasets: [{
      label: 'Purchase Amount',
      data: chartData.monthly.data,
      borderWidth: 2,
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: v => 'Rs. ' + v
        }
      }
    }
  }
});

// Switch mode
document.getElementById('chartMode').addEventListener('change', function () {
  const mode = this.value;
  purchaseChart.data.labels = chartData[mode].labels;
  purchaseChart.data.datasets[0].data = chartData[mode].data;
  purchaseChart.update();
});
</script>

@endsection
