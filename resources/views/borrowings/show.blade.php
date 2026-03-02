@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Detail Peminjaman #{{ $borrowing->id }}
        </h4>
        <p class="text-muted mb-0">Informasi lengkap transaksi</p>
    </div>
    <div class="d-flex gap-2">
        @if($borrowing->status === 'pending')
            <form action="{{ route('borrowings.approve', $borrowing->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success" 
                        onclick="return confirm('Setujui peminjaman ini?')">
                    <i class="bi bi-check-circle me-2"></i>Setujui
                </button>
            </form>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                <i class="bi bi-x-circle me-2"></i>Tolak
            </button>
        @endif
        
        @if($borrowing->status === 'approved')
            <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-primary" 
                        onclick="return confirm('Konfirmasi pengembalian alat?')">
                    <i class="bi bi-arrow-return-left me-2"></i>Kembalikan
                </button>
            </form>
        @endif
        
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi Peminjaman</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">ID Peminjaman</td>
                        <td class="fw-bold">#{{ $borrowing->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @php
                                $badgeClass = [
                                    'pending' => 'bg-warning',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'returned' => 'bg-info',
                                ][$borrowing->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                {{ ucfirst($borrowing->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Pinjam</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tanggal Kembali</td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                            @if($borrowing->status === 'approved' && \Carbon\Carbon::parse($borrowing->return_date)->isPast())
                                <br><span class="text-danger fw-bold">⚠️ Terlambat!</span>
                            @endif
                        </td>
                    </tr>
                    @if($borrowing->actual_return_date)
                    <tr>
                        <td class="text-muted">Tanggal Pengembalian</td>
                        <td>{{ \Carbon\Carbon::parse($borrowing->actual_return_date)->format('d M Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Catatan</td>
                        <td>{{ $borrowing->notes ?? '-' }}</td>
                    </tr>
                    @if($borrowing->rejection_reason)
                    <tr>
                        <td class="text-muted">Alasan Penolakan</td>
                        <td class="text-danger">{{ $borrowing->rejection_reason }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $borrowing->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @if($borrowing->approvedBy)
                    <tr>
                        <td class="text-muted">Disetujui Oleh</td>
                        <td>{{ $borrowing->approvedBy->name }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">👤 Peminjam</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" 
                         style="width: 50px; height: 50px; font-size: 1.5rem;">
                        {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $borrowing->user->name }}</h5>
                        <p class="text-muted mb-0">{{ $borrowing->user->email }}</p>
                        <small class="text-muted">{{ $borrowing->user->phone ?? '-' }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📦 Alat yang Dipinjam</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    @if($borrowing->item->image && file_exists(public_path('storage/' . $borrowing->item->image)))
                        <img src="{{ asset('storage/' . $borrowing->item->image) }}" 
                             alt="{{ $borrowing->item->name }}" 
                             class="rounded me-3" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center me-3" 
                             style="width: 80px; height: 80px;">
                            <i class="bi bi-box-seam text-muted fs-2"></i>
                        </div>
                    @endif
                    <div>
                        <h5 class="mb-0">{{ $borrowing->item->name }}</h5>
                        <p class="text-muted mb-0">{{ $borrowing->item->code }}</p>
                        <span class="badge bg-info">{{ $borrowing->item->category->name ?? '-' }}</span>
                        <span class="badge bg-{{ $borrowing->item->condition === 'baik' ? 'success' : 'warning' }}">
                            {{ $borrowing->item->condition }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
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
                        <textarea name="rejection_reason" class="form-control" rows="3" required 
                                  placeholder="Jelaskan alasan penolakan..."></textarea>
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
@endsection