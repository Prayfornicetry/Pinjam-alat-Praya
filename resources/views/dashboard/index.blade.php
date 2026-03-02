@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Overview
        </h4>
        <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Total Alat</h6>
                        <h3 class="mb-0">{{ $totalItems }}</h3>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
                <a href="{{ route('items.index') }}" class="text-white text-decoration-none small mt-2 d-block">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Pending</h6>
                        <h3 class="mb-0">{{ $pending }}</h3>
                    </div>
                    <i class="bi bi-clock-history fs-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.index') }}?status=pending" class="text-white text-decoration-none small mt-2 d-block">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Terlambat</h6>
                        <h3 class="mb-0">{{ $overdue }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.index') }}" class="text-white text-decoration-none small mt-2 d-block">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Selesai</h6>
                        <h3 class="mb-0">{{ $totalReturned }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.history') }}" class="text-white text-decoration-none small mt-2 d-block">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📈 Statistik Peminjaman (6 Bulan)</h6>
            </div>
            <div class="card-body">
                <canvas id="borrowingChart" height="120"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Low Stock -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Stok Menipis
                    </h6>
                    <a href="{{ route('items.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($lowStockItems->count() > 0)
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

<!-- Recent Activities -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">🕐 Aktivitas Terakhir</h6>
            <a href="{{ route('borrowings.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
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
                    @forelse($recentActivities as $activity)
                    <tr>
                        <td class="ps-4">{{ $activity->user->name ?? '-' }}</td>
                        <td>{{ $activity->item->name ?? '-' }}</td>
                        <td>{{ $activity->created_at->format('d M Y') }}</td>
                        <td>
                            @php
                                $badgeClass = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'returned' => 'bg-info',
                                ][$activity->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($activity->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3"></i>
                            <p class="mb-0 mt-2 small">Belum ada aktivitas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('borrowingChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            // ✅ Gunakan variabel array dari controller
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Peminjaman',
                // ✅ Gunakan variabel array dari controller
                data: {!! json_encode($chartData) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush
