@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Detail Peminjaman
        </h4>
        <p class="text-muted mb-0">Informasi peminjaman Anda</p>
    </div>
    <a href="{{ route('borrowings.my') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 150px;">ID Peminjaman</td>
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
                        <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
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
                        <td class="text-muted">Diajukan</td>
                        <td>{{ $borrowing->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm mb-4">
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
                    </div>
                </div>
            </div>
        </div>
        
        @if($borrowing->status === 'approved')
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Peminjaman disetujui!</strong> Silakan ambil alat sesuai jadwal.
        </div>
        @elseif($borrowing->status === 'pending')
        <div class="alert alert-warning">
            <i class="bi bi-hourglass-split me-2"></i>
            <strong>Menunggu approval</strong> Admin/staff akan segera memproses permintaan Anda.
        </div>
        @elseif($borrowing->status === 'rejected')
        <div class="alert alert-danger">
            <i class="bi bi-x-circle me-2"></i>
            <strong>Peminjaman ditolak</strong> Silakan hubungi admin untuk informasi lebih lanjut.
        </div>
        @elseif($borrowing->status === 'returned')
        <div class="alert alert-info">
            <i class="bi bi-check-circle me-2"></i>
            <strong>Alat sudah dikembalikan</strong> Terima kasih telah mengembalikan tepat waktu.
        </div>
        @endif
    </div>
</div>
@endsection