@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Overview
        </h4>
        <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Total Alat</h6>
                        <h3 class="mb-0">{{ $totalItems ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-tools fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('items.index') }}" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-warning text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Pending</h6>
                        <h3 class="mb-0">{{ $pending ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-clock-history fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.index') }}?status=pending" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-danger text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Terlambat</h6>
                        <h3 class="mb-0">{{ $overdue ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.index') }}" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Selesai</h6>
                        <h3 class="mb-0">{{ $totalReturned ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.history') }}" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg-info text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Total Dipinjam</h6>
                        <h3 class="mb-0">{{ $totalBorrowed ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-3 fs-md-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg-secondary text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Total User</h6>
                        <h3 class="mb-0">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-people fs-3 fs-md-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    @if(isset($efficiency))
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Approval Rate</h6>
                        <h3 class="mb-0">{{ $efficiency['approval_rate'] ?? 0 }}%</h3>
                    </div>
                    <i class="bi bi-graph-up fs-3 fs-md-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="row g-3 g-md-4">
    <!-- Chart Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-graph-up me-2 text-primary"></i>Statistik Peminjaman (6 Bulan)
                </h6>
            </div>
            <div class="card-body">
                <canvas id="borrowingChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Low Stock Items -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Stok Menipis
                    </h6>
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if(isset($lowStockItems) && $lowStockItems->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($lowStockItems as $item)
                    <div class="list-group-item px-3 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 small">{{ $item->name }}</h6>
                                <small class="text-muted">{{ $item->category->name ?? '-' }}</small>
                            </div>
                            <span class="badge bg-danger">{{ $item->stock_available }} Sisa</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle fs-1 text-success"></i>
                    <p class="text-muted small mt-2 mb-0">Semua stok aman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Category Stats -->
<div class="row g-3 g-md-4 mt-3">
<!-- Recent Activities -->
<div class="col-lg-8">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Aktivitas Terakhir
                </h6>
                <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Peminjam</th>
                            <th>Alat</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities ?? [] as $activity)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" 
                                         style="width: 35px; height: 35px;">
                                        {{ substr($activity->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 small">{{ $activity->user->name ?? '-' }}</h6>
                                        <small class="text-muted">{{ $activity->user->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $activity->item->name ?? '-' }}</td>
                            <td>
                                @if($activity->created_at)
                                    {{ $activity->created_at->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'pending' => 'bg-warning text-dark',
                                        'approved' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'returned' => 'bg-info text-dark',
                                    ][$activity->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ ucfirst($activity->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="bi bi-inbox fs-3 text-muted"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada aktivitas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Category Stats -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-pie-chart me-2 text-primary"></i>Kategori Populer
                </h6>
            </div>
            <div class="card-body p-0">
                @if(isset($categoryStats) && $categoryStats->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($categoryStats as $category)
                    <div class="list-group-item px-3 py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 small">{{ $category->name }}</h6>
                            <small class="text-muted">{{ $category->items_count }} alat</small>
                        </div>
                        <span class="badge bg-primary rounded-pill">{{ $category->items_count }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-folder fs-3 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada kategori</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Borrowing Chart
const ctx = document.getElementById('borrowingChart').getContext('2d');

// ✅ Data dari Controller
const chartLabels = @json($chartLabels ?? []);
const chartData = @json($chartData ?? []);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Peminjaman',
            data: chartData,
            borderColor: '#4a7c23',
            backgroundColor: 'rgba(74, 124, 35, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#4a7c23',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleFont: {
                    size: 13
                },
                bodyFont: {
                    size: 12
                },
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                },
                ticks: {
                    precision: 0,
                    font: {
                        size: 11
                    }
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});
</script>
@endpush