@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-receipt me-2 text-primary"></i>Detail Peminjaman #{{ $borrowing->id }}
        </h4>
        <p class="text-muted mb-0">Informasi lengkap transaksi peminjaman alat</p>
    </div>
    <div class="d-flex gap-2">
        @if($borrowing->status === 'pending' && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
            <i class="bi bi-check-circle me-2"></i>Setujui
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-circle me-2"></i>Tolak
        </button>
        @endif
        
        @if($borrowing->status === 'approved' && (Auth::user()->isAdmin() || Auth::user()->isStaff()))
        <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary" onclick="return confirm('Konfirmasi pengembalian alat?')">
                <i class="bi bi-arrow-return-left me-2"></i>Kembalikan
            </button>
        </form>
        @endif
        
        <a href="{{ route('borrowings.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <!-- Left Column - Borrowing Info -->
    <div class="col-lg-8">
        <!-- Status Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Status Peminjaman</h5>
                        <p class="text-muted mb-0">ID: #{{ str_pad($borrowing->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    @php
                        $badgeClass = [
                            'pending' => 'bg-warning text-dark',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'returned' => 'bg-info text-dark',
                        ][$borrowing->status] ?? 'bg-secondary';
                        
                        $statusLabel = [
                            'pending' => '⏳ Menunggu Approval',
                            'approved' => '✅ Disetujui',
                            'rejected' => '❌ Ditolak',
                            'returned' => '✓ Sudah Dikembalikan',
                        ][$borrowing->status] ?? $borrowing->status;
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2" style="font-size: 1rem;">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Borrowing Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi Peminjaman</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Tanggal Pinjam</small>
                            <h5 class="mb-0">{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</h5>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Tanggal Kembali</small>
                            <h5 class="mb-0">{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</h5>
                            @if($borrowing->status === 'approved' && \Carbon\Carbon::parse($borrowing->return_date)->isPast())
                            <small class="text-danger fw-bold">
                                <i class="bi bi-exclamation-triangle"></i> Terlambat!
                            </small>
                            @endif
                        </div>
                    </div>
                    @if($borrowing->actual_return_date)
                    <div class="col-md-6">
                        <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
                            <small class="text-success d-block">Tanggal Pengembalian</small>
                            <h5 class="mb-0 text-success">{{ \Carbon\Carbon::parse($borrowing->actual_return_date)->format('d M Y') }}</h5>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Durasi Pinjam</small>
                            <h5 class="mb-0">{{ $borrowing->total_days ?? '-' }} Hari</h5>
                        </div>
                    </div>
                </div>

                @if($borrowing->notes)
                <div class="mt-3">
                    <label class="form-label fw-bold">📝 Catatan / Keperluan:</label>
                    <p class="bg-light p-3 rounded mb-0">{{ $borrowing->notes }}</p>
                </div>
                @endif

                @if($borrowing->rejection_reason)
                <div class="mt-3">
                    <label class="form-label fw-bold text-danger">❌ Alasan Penolakan:</label>
                    <p class="bg-danger bg-opacity-10 p-3 rounded border border-danger mb-0">{{ $borrowing->rejection_reason }}</p>
                </div>
                @endif

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Dibuat</small>
                        <span class="fw-bold">{{ $borrowing->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($borrowing->approvedBy)
                    <div class="col-md-6">
                        <small class="text-muted d-block">Disetujui Oleh</small>
                        <span class="fw-bold">{{ $borrowing->approvedBy->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ✅ Rincian Harga (NEW!) -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💰 Rincian Harga</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted">Harga Sewa per Hari:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($borrowing->rental_price_per_day ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Durasi:</td>
                            <td class="text-end fw-bold">{{ $borrowing->total_days ?? 0 }} Hari</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Subtotal:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($borrowing->subtotal ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if($borrowing->discount_amount > 0)
                        <tr>
                            <td class="text-muted text-success">Diskon:</td>
                            <td class="text-end fw-bold text-success">- Rp {{ number_format($borrowing->discount_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($borrowing->discount_code)
                        <tr>
                            <td class="text-muted">Kode Diskon:</td>
                            <td class="text-end"><span class="badge bg-success">{{ $borrowing->discount_code }}</span></td>
                        </tr>
                        @endif
                        @endif
                        <tr>
                            <td class="text-muted">Total Setelah Diskon:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($borrowing->total_after_discount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if($borrowing->deposit_paid > 0)
                        <tr>
                            <td class="text-muted">Deposit/Jaminan:</td>
                            <td class="text-end fw-bold">Rp {{ number_format($borrowing->deposit_paid, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        @if($borrowing->late_fee > 0)
                        <tr>
                            <td class="text-muted text-warning">Denda Keterlambatan:</td>
                            <td class="text-end fw-bold text-warning">Rp {{ number_format($borrowing->late_fee, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="text-muted fw-bold">Total Bayar:</td>
                            <td class="text-end fw-bold text-primary" style="font-size: 1.25rem;">
                                Rp {{ number_format($borrowing->grand_total ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Payment Info -->
                @if($borrowing->payment_method || $borrowing->payment_status)
                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="mb-3 fw-bold">💳 Informasi Pembayaran</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Metode Pembayaran</small>
                            @php
                                $paymentMethodIcon = [
                                    'transfer' => '🏦',
                                    'qris' => '📱',
                                    'cash' => '💵',
                                ][$borrowing->payment_method] ?? '💳';
                            @endphp
                            <span class="fw-bold">{{ $paymentMethodIcon }} {{ ucfirst($borrowing->payment_method ?? '-') }}</span>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Status Pembayaran</small>
                            @php
                                $paymentStatusBadge = [
                                    'pending' => 'bg-warning text-dark',
                                    'paid' => 'bg-success',
                                    'refunded' => 'bg-info text-dark',
                                ][$borrowing->payment_status] ?? 'bg-secondary';
                                
                                $paymentStatusLabel = [
                                    'pending' => '⏳ Belum Dibayar',
                                    'paid' => '✅ Lunas',
                                    'refunded' => '💰 Dikembalikan',
                                ][$borrowing->payment_status] ?? '-';
                            @endphp
                            <span class="badge {{ $paymentStatusBadge }} px-3 py-2">{{ $paymentStatusLabel }}</span>
                        </div>
                        @if($borrowing->paid_at)
                        <div class="col-md-6">
                            <small class="text-muted d-block">Tanggal Bayar</small>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($borrowing->paid_at)->format('d M Y, H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - User & Item Info -->
    <div class="col-lg-4">
        <!-- Borrower Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">👤 Peminjam</h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" 
                         style="width: 60px; height: 60px; font-size: 1.5rem;">
                        {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $borrowing->user->name ?? '-' }}</h5>
                        <p class="text-muted mb-0">{{ $borrowing->user->email ?? '-' }}</p>
                        @if($borrowing->user->phone)
                        <small class="text-muted">📱 {{ $borrowing->user->phone }}</small>
                        @endif
                    </div>
                </div>
                <a href="{{ route('users.show', $borrowing->user->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-person me-2"></i>Lihat Profil
                </a>
            </div>
        </div>

        <!-- Item Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📦 Alat yang Dipinjam</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    @if($borrowing->item->image && file_exists(public_path('storage/' . $borrowing->item->image)))
                        <img src="{{ asset('storage/' . $borrowing->item->image) }}" 
                             alt="{{ $borrowing->item->name }}" 
                             class="img-fluid rounded" 
                             style="max-height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                             style="height: 200px;">
                            <i class="bi bi-box-seam text-muted" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                </div>
                
                <h5 class="mb-1">{{ $borrowing->item->name ?? '-' }}</h5>
                <p class="text-muted mb-2">{{ $borrowing->item->code ?? '-' }}</p>
                
                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-info">{{ $borrowing->item->category->name ?? '-' }}</span>
                    @php
                        $conditionBadge = [
                            'baik' => 'bg-success',
                            'rusak_ringan' => 'bg-warning',
                            'rusak_berat' => 'bg-danger'
                        ][$borrowing->item->condition] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $conditionBadge }}">{{ ucfirst($borrowing->item->condition) }}</span>
                </div>
                
                <a href="{{ route('items.show', $borrowing->item->id) }}" class="btn btn-sm btn-outline-primary w-100">
                    <i class="bi bi-eye me-2"></i>Lihat Detail Alat
                </a>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📅 Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-plus"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Diajukan</h6>
                            <small class="text-muted">{{ $borrowing->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($borrowing->approved_by && $borrowing->status !== 'pending')
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <div class="{{ $borrowing->status === 'rejected' ? 'bg-danger' : 'bg-success' }} text-white rounded-circle d-flex justify-content-center align-items-center" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-{{ $borrowing->status === 'rejected' ? 'x' : 'check' }}"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $borrowing->status === 'rejected' ? 'Ditolak' : 'Disetujui' }}</h6>
                            <small class="text-muted">Oleh: {{ $borrowing->approvedBy->name ?? '-' }}</small>
                        </div>
                    </div>
                    @endif
                    
                    @if($borrowing->actual_return_date)
                    <div class="d-flex">
                        <div class="me-3">
                            <div class="bg-info text-white rounded-circle d-flex justify-content-center align-items-center" 
                                 style="width: 30px; height: 30px;">
                                <i class="bi bi-arrow-return-left"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0">Dikembalikan</h6>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($borrowing->actual_return_date)->format('d M Y') }}</small>
                            @if($borrowing->late_fee > 0)
                            <br><small class="text-warning">⚠️ Denda: Rp {{ number_format($borrowing->late_fee, 0, ',', '.') }}</small>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('borrowings.approve', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">✅ Setujui Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Stok alat akan berkurang sebanyak 1 setelah approval.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('borrowings.reject', $borrowing->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">❌ Tolak Peminjaman</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Peminjam akan mendapat notifikasi penolakan dengan alasan ini.
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