@extends('layouts.app')

@section('title', 'Dashboard Staff')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Staff
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
                        <h6 class="mb-0 opacity-75">Total Peminjaman</h6>
                        <h3 class="mb-0">{{ $totalBorrowings }}</h3>
                    </div>
                    <i class="bi bi-calendar-check fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Perlu Approval</h6>
                        <h3 class="mb-0">{{ $pending }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split fs-1 opacity-50"></i>
                </div>
                <a href="{{ route('borrowings.index') }}?status=pending" class="text-white text-decoration-none small mt-2 d-block">
                    Approve Sekarang <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Disetujui</h6>
                        <h3 class="mb-0">{{ $approved }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
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
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">⏳ Perlu Approval ({{ $pending }})</h6>
            <a href="{{ route('borrowings.index') }}?status=pending" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($pendingBorrowings->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingBorrowings as $borrowing)
                    <tr>
                        <td class="ps-4">
                            <div>
                                <h6 class="mb-0">{{ $borrowing->user->name ?? '-' }}</h6>
                                <small class="text-muted">{{ $borrowing->user->email ?? '-' }}</small>
                            </div>
                        </td>
                        <td>{{ $borrowing->item->name ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <form action="{{ route('borrowings.approve', $borrowing->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rejectModal{{ $borrowing->id }}" 
                                        title="Tolak">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                            
                            <!-- Modal Reject -->
                            <div class="modal fade" id="rejectModal{{ $borrowing->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('borrowings.reject', $borrowing->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Peminjaman</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan</label>
                                                    <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-check-circle fs-1 text-success"></i>
            <p class="text-muted mt-2 mb-0">Tidak ada peminjaman yang perlu approval</p>
        </div>
        @endif
    </div>
</div>

<!-- Recent Activities -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">🕐 Aktivitas Terakhir</h6>
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