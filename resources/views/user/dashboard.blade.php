@extends('layouts.app')

@section('title', 'Dashboard Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Saya
        </h4>
        <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->name }}!</p>
    </div>
    <a href="{{ route('borrowing.request.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i><span class="d-none d-sm-inline">Ajukan </span>Peminjaman
    </a>
</div>

<!-- Stats Cards -->
<div class="row g-3 g-md-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Total Pinjam</h6>
                        <h3 class="mb-0">{{ $totalBorrowings }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.my') }}" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-warning text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Sedang Dipinjam</h6>
                        <h3 class="mb-0">{{ $active }}</h3>
                    </div>
                    <i class="bi bi-clock-history fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.my') }}?status=approved" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-info text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Menunggu Approval</h6>
                        <h3 class="mb-0">{{ $pending }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.my') }}?status=pending" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body py-3 py-md-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75 small">Selesai</h6>
                        <h3 class="mb-0">{{ $returned }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-3 fs-md-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.my') }}?status=returned" class="text-white text-decoration-none small mt-2 d-block opacity-75">
                    Lihat Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-md-4">
    <!-- My Borrowings -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Peminjaman Saya
                    </h6>
                    <a href="{{ route('borrowings.my') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-list-ul me-1 d-none d-sm-inline"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($myBorrowings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Alat</th>
                                <th class="d-none d-md-table-cell">Tgl Pinjam</th>
                                <th class="d-none d-md-table-cell">Tgl Kembali</th>
                                <th class="d-md-none">Tanggal</th>
                                <th>Status</th>
                                <th class="text-end pe-3 pe-md-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myBorrowings as $borrowing)
                            <tr>
                                <td class="ps-4">
                                    <div>
                                        <h6 class="mb-0 small">{{ $borrowing->item->name ?? '-' }}</h6>
                                        <small class="text-muted d-none d-md-block">{{ $borrowing->item->code ?? '-' }}</small>
                                    </div>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</small>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <small>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</small>
                                </td>
                                <td class="d-md-none">
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d/m') }}
                                        <i class="bi bi-arrow-right mx-1"></i>
                                        {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d/m') }}
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'returned' => 'bg-info text-dark',
                                        ][$borrowing->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} small">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3 pe-md-4">
                                    <a href="{{ route('borrowings.my.show', $borrowing->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Detail">
                                        <i class="bi bi-eye"></i>
                                        <span class="d-none d-md-inline ms-1">Detail</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada riwayat peminjaman</p>
                    <a href="{{ route('borrowing.request.create') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman Pertama
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Available Items -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Alat Tersedia
                    </h6>
                    <a href="{{ route('items.user.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-list-ul me-1 d-none d-sm-inline"></i>Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($availableItems->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($availableItems as $item)
                    <a href="{{ route('items.show', $item->id) }}" class="list-group-item list-group-item-action px-3 py-3 border-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small text-truncate" style="max-width: 200px;">{{ $item->name }}</h6>
                                <small class="text-muted">{{ $item->category->name ?? '-' }}</small>
                            </div>
                            <span class="badge bg-success ms-2">{{ $item->stock_available }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-box-seam fs-1 text-muted"></i>
                    <p class="text-muted small mt-2 mb-0">Tidak ada alat tersedia</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions (Mobile Friendly) -->
<div class="row g-3 mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">⚡ Aksi Cepat</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('borrowing.request.create') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-plus-circle d-block mb-1" style="font-size: 1.5rem;"></i>
                            <small>Ajukan Peminjaman</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('borrowings.my') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-calendar-check d-block mb-1" style="font-size: 1.5rem;"></i>
                            <small>Peminjaman Saya</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('borrowings.history') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-clock-history d-block mb-1" style="font-size: 1.5rem;"></i>
                            <small>Riwayat</small>
                        </a>
                    </div>
                    <div class="col-6 col-md-3">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-person d-block mb-1" style="font-size: 1.5rem;"></i>
                            <small>Profil Saya</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection