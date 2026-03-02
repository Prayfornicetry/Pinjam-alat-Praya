@extends('layouts.app')

@section('title', 'Riwayat Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Peminjaman
        </h4>
        <p class="text-muted mb-0">Daftar semua peminjaman yang sudah dikembalikan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Peminjaman
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Total Dikembalikan</h6>
                        <h3 class="mb-0">{{ $totalReturned ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-arrow-return-left fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Tepat Waktu</h6>
                        <h3 class="mb-0">{{ $totalOnTime ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Terlambat Kembali</h6>
                        <h3 class="mb-0">{{ $totalLate ?? 0 }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4 no-print">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">
            <i class="bi bi-funnel me-2"></i>Filter Riwayat
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('borrowings.history') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label small text-muted">🔍 Cari</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Nama peminjam atau alat..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">📅 Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" 
                       value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">📅 Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" 
                       value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
        
        @if(request()->anyFilled(['search', 'start_date', 'end_date']))
        <div class="mt-3">
            <a href="{{ route('borrowings.history') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Reset Filter
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-list-ul me-2"></i>Daftar Riwayat ({{ $borrowings->total() }} Total)
            </h6>
            <small class="text-muted">
                Menampilkan {{ $borrowings->firstItem() ?? 0 }} - {{ $borrowings->lastItem() ?? 0 }}
            </small>
        </div>
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
                        <th>Tgl Rencana Kembali</th>
                        <th>Tgl Aktual Kembali</th>
                        <th>Status Pengembalian</th>
                        <th>Disetujui Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                    @php
                        $isLate = \Carbon\Carbon::parse($borrowing->actual_return_date)
                                    ->isAfter(\Carbon\Carbon::parse($borrowing->return_date));
                        
                        $lateDays = $isLate ? 
                            \Carbon\Carbon::parse($borrowing->actual_return_date)
                                ->diffInDays(\Carbon\Carbon::parse($borrowing->return_date)) : 0;
                    @endphp
                    <tr class="{{ $isLate ? 'table-warning' : '' }}">
                        <td class="ps-4">
                            <span class="badge bg-secondary">#{{ $borrowing->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" 
                                     style="width: 35px; height: 35px;">
                                    {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $borrowing->user->name ?? '-' }}</h6>
                                    <small class="text-muted">{{ $borrowing->user->email ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $borrowing->item->name ?? '-' }}</h6>
                                <small class="text-muted">{{ $borrowing->item->code ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                        </td>
                        <td>
                            <div>
                                {{ \Carbon\Carbon::parse($borrowing->actual_return_date)->format('d M Y') }}
                                @if($isLate)
                                    <br>
                                    <small class="text-danger fw-bold">
                                        <i class="bi bi-exclamation-triangle"></i> 
                                        +{{ $lateDays }} hari
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($isLate)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Terlambat
                                </span>
                            @else
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Tepat Waktu
                                </span>
                            @endif
                        </td>
                        <td>
                            {{ $borrowing->approvedBy->name ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-clock-history fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada riwayat peminjaman</p>
                            <small class="text-muted">Riwayat akan muncul setelah ada alat yang dikembalikan</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $borrowings->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Print Style -->
<style>
    @media print {
        .no-print, .sidebar, .topbar, .pagination, .btn {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }
</style>
@endsection