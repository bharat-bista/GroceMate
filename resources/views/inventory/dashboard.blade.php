@extends('inventory.layouts.inventory')

@section('title','Inventory Dashboard')
@section('heading','Dashboard')
@section('subtitle','Overview of products & alerts')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap');

:root {
  --ink: #0a0a0f;
  --ink-soft: #3d3d4a;
  --ink-muted: #7c7c8e;
  --surface: #ffffff;
  --surface-2: #f5f5f8;
  --surface-3: #eeeeF2;
  --accent: #4f46e5;
  --accent-2: #06b6d4;
  --accent-3: #f59e0b;
  --accent-4: #10b981;
  --accent-danger: #ef4444;
  --border: #e4e4ed;
  --radius: 16px;
  --radius-sm: 10px;
}

.dash-wrap * { font-family: 'DM Sans', sans-serif; box-sizing: border-box; }
.dash-wrap h1, .dash-wrap h2, .dash-wrap h3, .dash-wrap .syne { font-family: 'Syne', sans-serif; }

.dash-wrap {
  background: var(--surface-2);
  min-height: 100vh;
  padding: 0 0 48px 0;
}

/* ── Top Banner ── */
.dash-banner {
  background: var(--ink);
  background-image: 
    radial-gradient(circle at 20% 50%, rgba(79,70,229,0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(6,182,212,0.2) 0%, transparent 40%);
  padding: 36px 32px 28px;
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.dash-banner-title {
  color: #fff;
  font-family: 'Syne', sans-serif;
  font-size: 28px;
  font-weight: 800;
  letter-spacing: -0.5px;
  line-height: 1;
  margin: 0 0 6px;
}

.dash-banner-sub {
  color: rgba(255,255,255,0.5);
  font-size: 13px;
  margin: 0;
}

.dash-date {
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.12);
  color: rgba(255,255,255,0.7);
  font-size: 12px;
  padding: 8px 14px;
  border-radius: 100px;
  backdrop-filter: blur(10px);
  white-space: nowrap;
}

/* ── Content Area ── */
.dash-content { padding: 28px 32px; }

/* ── KPI Grid ── */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 14px;
  margin-bottom: 28px;
}

@media (max-width: 1200px) { .kpi-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 700px)  { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }

.kpi-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 20px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: default;
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(0,0,0,0.08);
}

.kpi-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 3px;
  border-radius: var(--radius) var(--radius) 0 0;
}

.kpi-card.c1::before { background: linear-gradient(90deg, var(--accent), #818cf8); }
.kpi-card.c2::before { background: linear-gradient(90deg, var(--accent-4), #34d399); }
.kpi-card.c3::before { background: linear-gradient(90deg, var(--accent-3), #fcd34d); }
.kpi-card.c4::before { background: linear-gradient(90deg, var(--accent-2), #67e8f9); }
.kpi-card.c5::before { background: linear-gradient(90deg, #f97316, #fb923c); }
.kpi-card.c6::before { background: linear-gradient(90deg, var(--accent-danger), #f87171); }

.kpi-icon {
  width: 38px; height: 38px;
  border-radius: var(--radius-sm);
  display: flex; align-items: center; justify-content: center;
  margin-bottom: 14px;
  font-size: 17px;
}

.kpi-card.c1 .kpi-icon { background: #eef2ff; color: var(--accent); }
.kpi-card.c2 .kpi-icon { background: #ecfdf5; color: var(--accent-4); }
.kpi-card.c3 .kpi-icon { background: #fffbeb; color: var(--accent-3); }
.kpi-card.c4 .kpi-icon { background: #ecfeff; color: var(--accent-2); }
.kpi-card.c5 .kpi-icon { background: #fff7ed; color: #f97316; }
.kpi-card.c6 .kpi-icon { background: #fef2f2; color: var(--accent-danger); }

.kpi-label {
  font-size: 11px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ink-muted);
  margin-bottom: 4px;
}

.kpi-value {
  font-family: 'Syne', sans-serif;
  font-size: 30px;
  font-weight: 800;
  color: var(--ink);
  line-height: 1;
}

/* ── Main Grid ── */
.main-grid {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 20px;
}

@media (max-width: 1000px) { .main-grid { grid-template-columns: 1fr; } }

/* ── Panel ── */
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}

.panel-head {
  padding: 20px 24px 16px;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.panel-title {
  font-family: 'Syne', sans-serif;
  font-size: 15px;
  font-weight: 700;
  color: var(--ink);
}

.panel-link {
  font-size: 12px;
  color: var(--accent);
  text-decoration: none;
  font-weight: 500;
  padding: 5px 12px;
  background: #eef2ff;
  border-radius: 100px;
  transition: background 0.15s;
}
.panel-link:hover { background: #e0e7ff; }

/* ── Chart Select ── */
.chart-select {
  font-size: 12px;
  font-family: 'DM Sans', sans-serif;
  font-weight: 500;
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 5px 12px;
  color: var(--ink-soft);
  background: var(--surface-2);
  outline: none;
  cursor: pointer;
}

/* ── Chart Wrap ── */
.chart-wrap { padding: 20px 24px 24px; }

/* ── Chart Legend ── */
.chart-legend {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.chart-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  color: var(--ink-muted);
}

.chart-legend-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  background: var(--accent);
}

/* ── Stat Summary Row ── */
.stat-row {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1px;
  background: var(--border);
  border-top: 1px solid var(--border);
}

.stat-cell {
  background: var(--surface);
  padding: 14px 20px;
  text-align: center;
}

.stat-cell-label { font-size: 11px; color: var(--ink-muted); text-transform: uppercase; letter-spacing: 0.05em; }
.stat-cell-val   { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700; color: var(--ink); margin-top: 2px; }

/* ── Low Stock Table ── */
.stock-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.stock-table thead th {
  padding: 10px 16px;
  text-align: left;
  font-size: 11px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ink-muted);
  background: var(--surface-2);
  border-bottom: 1px solid var(--border);
}

.stock-table tbody tr { border-bottom: 1px solid var(--surface-3); transition: background 0.1s; }
.stock-table tbody tr:last-child { border-bottom: none; }
.stock-table tbody tr:hover { background: var(--surface-2); }
.stock-table td { padding: 12px 16px; color: var(--ink-soft); }
.stock-table td:first-child { color: var(--ink); font-weight: 500; }

/* ── Stock Badge ── */
.stock-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 100px;
}

.stock-badge.critical { background: #fef2f2; color: #dc2626; }
.stock-badge.warning  { background: #fffbeb; color: #d97706; }
.stock-badge.ok       { background: #f0fdf4; color: #16a34a; }

/* ── Alert Panel ── */
.alert-list { padding: 8px 0; }
.alert-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  padding: 12px 20px;
  border-bottom: 1px solid var(--surface-3);
  transition: background 0.1s;
}
.alert-item:last-child { border-bottom: none; }
.alert-item:hover { background: var(--surface-2); }

.alert-dot {
  width: 8px; height: 8px;
  border-radius: 50%;
  margin-top: 5px;
  flex-shrink: 0;
}
.alert-dot.red    { background: var(--accent-danger); }
.alert-dot.yellow { background: var(--accent-3); }
.alert-dot.blue   { background: var(--accent); }

.alert-text { font-size: 13px; color: var(--ink-soft); line-height: 1.5; }
.alert-text strong { color: var(--ink); font-weight: 600; }
.alert-time { font-size: 11px; color: var(--ink-muted); margin-top: 2px; }

/* ── Animations ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

.kpi-card { animation: fadeUp 0.4s ease both; }
.kpi-card:nth-child(1) { animation-delay: 0.05s; }
.kpi-card:nth-child(2) { animation-delay: 0.10s; }
.kpi-card:nth-child(3) { animation-delay: 0.15s; }
.kpi-card:nth-child(4) { animation-delay: 0.20s; }
.kpi-card:nth-child(5) { animation-delay: 0.25s; }
.kpi-card:nth-child(6) { animation-delay: 0.30s; }

.panel { animation: fadeUp 0.5s ease 0.3s both; }
</style>

<div class="dash-wrap">

  {{-- ── Banner ── --}}
  <div class="dash-banner">
    <div>
      <p class="dash-banner-title">Inventory Dashboard</p>
      <p class="dash-banner-sub">Overview of products, stock levels & purchase activity</p>
    </div>
    <div class="dash-date">
      📅 {{ now()->format('l, d M Y') }}
    </div>
  </div>

  <div class="dash-content">

    {{-- ── KPI Cards ── --}}
    <div class="kpi-grid">

      <div class="kpi-card c1">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
            <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
          </svg>
        </div>
        <div class="kpi-label">Total Products</div>
        <div class="kpi-value">{{ $totalProducts }}</div>
      </div>

      <div class="kpi-card c2">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div class="kpi-label">Active Products</div>
        <div class="kpi-value">{{ $activeProducts }}</div>
      </div>

      <div class="kpi-card c3">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          </svg>
        </div>
        <div class="kpi-label">Low Stock</div>
        <div class="kpi-value">{{ $lowStockCount }}</div>
      </div>

      <div class="kpi-card c4">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
          </svg>
        </div>
        <div class="kpi-label">Total Suppliers</div>
        <div class="kpi-value">{{ $supplierCount }}</div>
      </div>

      <div class="kpi-card c5">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8"  y1="2" x2="8"  y2="6"/>
            <line x1="3"  y1="10" x2="21" y2="10"/>
          </svg>
        </div>
        <div class="kpi-label">Expiring Soon</div>
        <div class="kpi-value">{{ $expiringSoonCount }}</div>
      </div>

      <div class="kpi-card c6">
        <div class="kpi-icon">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
          </svg>
        </div>
        <div class="kpi-label">Expired</div>
        <div class="kpi-value">{{ $expiredCount }}</div>
      </div>

    </div>

    {{-- ── Main Grid ── --}}
    <div class="main-grid">

      {{-- Chart Panel --}}
      <div class="panel">
        <div class="panel-head">
          <span class="panel-title">Purchase Overview</span>
          <select id="chartMode" class="chart-select">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly" selected>Monthly</option>
            <option value="yearly">Yearly</option>
          </select>
        </div>
        <div class="chart-wrap">
          <div class="chart-legend">
            <div class="chart-legend-item">
              <div class="chart-legend-dot"></div>
              Purchase Amount (NPR)
            </div>
          </div>
          <canvas id="purchaseChart" height="110"></canvas>
        </div>
        <div class="stat-row">
          <div class="stat-cell">
            <div class="stat-cell-label">Total Products</div>
            <div class="stat-cell-val">{{ $totalProducts }}</div>
          </div>
          <div class="stat-cell">
            <div class="stat-cell-label">Active</div>
            <div class="stat-cell-val">{{ $activeProducts }}</div>
          </div>
          <div class="stat-cell">
            <div class="stat-cell-label">Suppliers</div>
            <div class="stat-cell-val">{{ $supplierCount }}</div>
          </div>
        </div>
      </div>

      {{-- Right Column --}}
      <div style="display:flex;flex-direction:column;gap:20px;">

        {{-- Low Stock Panel --}}
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">⚠ Low Stock</span>
            <a href="{{ route('inventory.products.index') }}" class="panel-link">View all</a>
          </div>
          <table class="stock-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Reorder</th>
              </tr>
            </thead>
            <tbody>
              @forelse($topLowStock as $row)
                <tr>
                  <td>{{ $row->product->name ?? '—' }}</td>
                  <td>
                    @php $qty = $row->quantity; $lvl = $row->reorder_level; @endphp
                    <span class="stock-badge {{ $qty == 0 ? 'critical' : ($qty <= $lvl ? 'warning' : 'ok') }}">
                      {{ $qty == 0 ? '⬤' : ($qty <= $lvl ? '⬤' : '⬤') }}
                      {{ $qty }}
                    </span>
                  </td>
                  <td style="color:var(--ink-muted)">{{ $lvl }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" style="padding:20px 16px;text-align:center;color:var(--ink-muted);font-size:13px;">
                    ✓ All stock levels healthy
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Alerts Panel --}}
        <div class="panel">
          <div class="panel-head">
            <span class="panel-title">🔔 Alerts</span>
          </div>
          <div class="alert-list">
            @if($expiredCount > 0)
              <div class="alert-item">
                <div class="alert-dot red"></div>
                <div>
                  <div class="alert-text"><strong>{{ $expiredCount }} product(s)</strong> have expired and need removal.</div>
                  <div class="alert-time">Action required</div>
                </div>
              </div>
            @endif
            @if($expiringSoonCount > 0)
              <div class="alert-item">
                <div class="alert-dot yellow"></div>
                <div>
                  <div class="alert-text"><strong>{{ $expiringSoonCount }} product(s)</strong> expiring within 30 days.</div>
                  <div class="alert-time">Monitor closely</div>
                </div>
              </div>
            @endif
            @if($lowStockCount > 0)
              <div class="alert-item">
                <div class="alert-dot blue"></div>
                <div>
                  <div class="alert-text"><strong>{{ $lowStockCount }} item(s)</strong> below reorder level.</div>
                  <div class="alert-time">Restock recommended</div>
                </div>
              </div>
            @endif
            @if($expiredCount == 0 && $expiringSoonCount == 0 && $lowStockCount == 0)
              <div class="alert-item">
                <div class="alert-dot" style="background:#10b981"></div>
                <div>
                  <div class="alert-text"><strong>All clear!</strong> No alerts at this time.</div>
                  <div class="alert-time">System healthy</div>
                </div>
              </div>
            @endif
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

{{-- Chart Data + JS --}}
<script>
const chartData = {
  daily: {
    labels: @json($dailyPurchases->map(fn($p) => \Carbon\Carbon::parse($p->label)->format('M d'))->values()->all()),
    data:   @json($dailyPurchases->pluck('total')->values()->all()),
  },
  weekly: {
    labels: @json($weeklyPurchases->map(fn($p) => "Week " . substr($p->label, -2))->values()->all()),
    data:   @json($weeklyPurchases->pluck('total')->values()->all()),
  },
  monthly: {
    labels: @json($monthlyPurchases->map(fn($p) => \Carbon\Carbon::parse($p->label . "-01")->format('M Y'))->values()->all()),
    data:   @json($monthlyPurchases->pluck('total')->values()->all()),
  },
  yearly: {
    labels: @json($yearlyPurchases->pluck('label')->values()->all()),
    data:   @json($yearlyPurchases->pluck('total')->values()->all()),
  },
};
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('purchaseChart').getContext('2d');

const gradient = ctx.createLinearGradient(0, 0, 0, 300);
gradient.addColorStop(0, 'rgba(79,70,229,0.18)');
gradient.addColorStop(1, 'rgba(79,70,229,0)');

let purchaseChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: chartData.monthly.labels,
    datasets: [{
      label: 'Purchase Amount',
      data: chartData.monthly.data,
      borderColor: '#4f46e5',
      borderWidth: 2.5,
      pointBackgroundColor: '#4f46e5',
      pointBorderColor: '#fff',
      pointBorderWidth: 2,
      pointRadius: 4,
      pointHoverRadius: 6,
      tension: 0.4,
      fill: true,
      backgroundColor: gradient,
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#0a0a0f',
        titleColor: 'rgba(255,255,255,0.6)',
        bodyColor: '#fff',
        bodyFont: { family: 'DM Sans', size: 13, weight: '500' },
        padding: 12,
        cornerRadius: 10,
        callbacks: {
          label: ctx => ' Rs. ' + ctx.parsed.y.toLocaleString()
        }
      }
    },
    scales: {
      x: {
        grid: { display: false },
        border: { display: false },
        ticks: {
          color: '#7c7c8e',
          font: { family: 'DM Sans', size: 11 }
        }
      },
      y: {
        grid: { color: '#f0f0f5', drawBorder: false },
        border: { display: false, dash: [4,4] },
        beginAtZero: true,
        ticks: {
          color: '#7c7c8e',
          font: { family: 'DM Sans', size: 11 },
          callback: v => 'Rs.' + (v >= 1000 ? (v/1000).toFixed(0)+'k' : v)
        }
      }
    }
  }
});

document.getElementById('chartMode').addEventListener('change', function () {
  const mode = this.value;
  purchaseChart.data.labels = chartData[mode].labels;
  purchaseChart.data.datasets[0].data = chartData[mode].data;
  purchaseChart.update('active');
});
</script>

@endsection