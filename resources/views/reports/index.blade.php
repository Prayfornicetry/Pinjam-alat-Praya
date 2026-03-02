@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Peminjaman
        </h4>
        <p class="text-muted mb-0">Analisis dan statistik peminjaman alat</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.pdf', request()->all()) }}" target="_blank" class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
        </a>
        <a href="{{ route('reports.excel', request()->all()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- Filter -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small text-muted">📅 Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">📅 Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">📊 Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small text-muted">📦 Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach(\App\Models\Category::all() as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Total</h6>
                <h3 class="mb-0">{{ $totalBorrowings }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Pending</h6>
                <h3 class="mb-0">{{ $totalPending }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Disetujui</h6>
                <h3 class="mb-0">{{ $totalApproved }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Ditolak</h6>
                <h3 class="mb-0">{{ $totalRejected }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Kembali</h6>
                <h3 class="mb-0">{{ $totalReturned }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-body text-center">
                <h6 class="mb-0 opacity-75">Periode</h6>
                <h6 class="mb-0">{{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M') }}</h6>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📈 Grafik Peminjaman per Hari</h6>
            </div>
            <div class="card-body">
                <canvas id="borrowingChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">🏆 Top 5 Peminjam</h6>
            </div>
            <div class="card-body">
                @if($topBorrowers->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($topBorrowers as $index => $user)
                    <div class="list-group-item px-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2" style="width: 25px;">{{ $index + 1 }}</span>
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                            <span class="badge bg-info">{{ $user->borrowings_count }}x</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted text-center">Belum ada data</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Items -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">📦 Top 5 Alat Paling Dipinjam</h6>
    </div>
    <div class="card-body">
        @if($topItems->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Rank</th>
                        <th>Alat</th>
                        <th>Kategori</th>
                        <th>Jumlah Pinjam</th>
                        <th>Stok Tersedia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topItems as $index => $item)
                    <tr>
                        <td><span class="badge bg-warning text-dark">{{ $index + 1 }}</span></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td><span class="badge bg-primary">{{ $item->borrowings_count }}x</span></td>
                        <td>{{ $item->stock_available }} / {{ $item->stock_total }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-muted text-center mb-0">Belum ada data</p>
        @endif
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">📋 Detail Peminjaman ({{ $borrowings->count() }} Data)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Disetujui</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                    <tr>
                        <td class="ps-4">#{{ $borrowing->id }}</td>
                        <td>{{ $borrowing->user->name ?? '-' }}</td>
                        <td>{{ $borrowing->item->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
                        <td>
                            <span class="badge bg-{{ $borrowing->status === 'approved' ? 'success' : ($borrowing->status === 'pending' ? 'warning' : ($borrowing->status === 'returned' ? 'info' : 'danger')) }}">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </td>
                        <td>{{ $borrowing->approvedBy->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Tidak ada data peminjaman pada periode ini</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Print Style -->
<style>
    @media print {
        .no-print, .sidebar, .topbar, .btn, .pagination {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('borrowingChart').getContext('2d');
    const borrowingChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: {!! json_encode($chartData['data']) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#4e73df',
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
                    backgroundColor: '#1a1c23',
                    padding: 12,
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endpush