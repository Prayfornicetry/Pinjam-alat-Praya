@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-eye me-2 text-primary"></i>Detail Alat
        </h4>
        <p class="text-muted mb-0">Informasi lengkap inventaris alat</p>
    </div>
    <div class="d-flex gap-2">
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning text-white">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        @endif
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
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
    <!-- Left Column - Item Info -->
    <div class="col-lg-5">
        <!-- Item Image -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body p-0">
                @if($item->image && file_exists(public_path('storage/' . $item->image)))
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="img-fluid w-100 rounded-top" 
                         style="max-height: 300px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" 
                         style="height: 300px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 5rem;"></i>
                    </div>
                @endif
                
                <!-- Stock Status Badge -->
                @if($item->stock_available <= 0)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-danger px-3 py-2" style="font-size: 0.9rem;">
                            <i class="bi bi-x-circle me-1"></i>Stok Habis
                        </span>
                    </div>
                @elseif($item->stock_available <= 2)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.9rem;">
                            <i class="bi bi-exclamation-triangle me-1"></i>Stok Tersisa {{ $item->stock_available }}
                        </span>
                    </div>
                @else
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;">
                            <i class="bi bi-check-circle me-1"></i>Tersedia
                        </span>
                    </div>
                @endif
                
                <!-- ✅ DISCOUNT BADGE (MORE PROMINENT) -->
                @if($item->has_discount && $item->discount_percentage > 0 && $item->hasActiveDiscount())
                <div class="position-absolute top-0 start-0 m-3">
                    <span class="badge bg-danger px-4 py-3" style="font-size: 1rem; animation: pulse 2s infinite;">
                        <i class="bi bi-percent me-1"></i>Diskon {{ $item->discount_percentage }}%
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm bg-success text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Tersedia</h6>
                        <h3 class="mb-0">{{ $item->stock_available }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm bg-primary text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Total Stok</h6>
                        <h3 class="mb-0">{{ $item->stock_total }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- ✅ INFORMASI HARGA (IMPROVED) -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💰 Informasi Harga</h6>
            </div>
            <div class="card-body">
                @php
                    $hasActiveDiscount = $item->has_discount && $item->discount_percentage > 0 && $item->hasActiveDiscount();
                    $discountedPrice = $hasActiveDiscount 
                        ? $item->rental_price - ($item->rental_price * $item->discount_percentage / 100)
                        : $item->rental_price;
                    $discountedMemberPrice = $hasActiveDiscount 
                        ? $item->member_price - ($item->member_price * $item->discount_percentage / 100)
                        : $item->member_price;
                    $savings = $item->rental_price - $discountedPrice;
                @endphp

                <div class="row g-3">
                    <!-- Harga Normal -->
                    <div class="col-6">
                        <div class="p-3 {{ $hasActiveDiscount ? 'bg-light' : 'bg-primary bg-opacity-10' }} rounded text-center" 
                             style="border: 2px solid {{ $hasActiveDiscount ? '#e0e0e0' : '#556b2f' }};">
                            <small class="text-muted d-block mb-2">Harga Normal</small>
                            @if($hasActiveDiscount)
                                <div class="text-decoration-line-through text-muted mb-1" style="font-size: 0.9rem;">
                                    Rp {{ number_format($item->rental_price, 0, ',', '.') }}
                                </div>
                            @endif
                            <h4 class="mb-0 {{ $hasActiveDiscount ? 'text-dark' : 'text-primary' }} fw-bold" 
                                style="font-size: 1.3rem;">
                                Rp {{ number_format($discountedPrice, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">/hari</small>
                            @if($hasActiveDiscount)
                                <div class="mt-2">
                                    <span class="badge bg-success" style="font-size: 0.75rem;">
                                        Hemat Rp {{ number_format($savings, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Harga Member -->
                    <div class="col-6">
                        <div class="p-3 {{ $hasActiveDiscount ? 'bg-light' : 'bg-success bg-opacity-10' }} rounded text-center"
                             style="border: 2px solid {{ $hasActiveDiscount ? '#e0e0e0' : '#6b8e23' }};">
                            <small class="text-muted d-block mb-2">Harga Member</small>
                            @if($hasActiveDiscount)
                                <div class="text-decoration-line-through text-muted mb-1" style="font-size: 0.9rem;">
                                    Rp {{ number_format($item->member_price, 0, ',', '.') }}
                                </div>
                            @endif
                            <h4 class="mb-0 {{ $hasActiveDiscount ? 'text-dark' : 'text-success' }} fw-bold"
                                style="font-size: 1.3rem;">
                                Rp {{ number_format($discountedMemberPrice, 0, ',', '.') }}
                            </h4>
                            <small class="text-muted">/hari</small>
                            @if($hasActiveDiscount)
                                <div class="mt-2">
                                    <span class="badge bg-success" style="font-size: 0.75rem;">
                                        Hemat Rp {{ number_format($item->member_price - $discountedMemberPrice, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Denda -->
                    <div class="col-6">
                        <div class="p-3 bg-warning bg-opacity-10 rounded text-center" 
                             style="border: 2px solid #daa520;">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>Denda Terlambat
                            </small>
                            <h5 class="mb-0 text-warning fw-bold">
                                Rp {{ number_format($item->late_fee ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/hari</small>
                        </div>
                    </div>

                    <!-- Deposit -->
                    <div class="col-6">
                        <div class="p-3 bg-info bg-opacity-10 rounded text-center"
                             style="border: 2px solid #5f9ea0;">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-shield-check text-info me-1"></i>Deposit
                            </small>
                            <h5 class="mb-0 text-info fw-bold">
                                Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/pinjam</small>
                        </div>
                    </div>
                </div>

                <!-- ✅ DISCOUNT INFO BANNER (MORE VISIBLE) -->
                @if($hasActiveDiscount)
                <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded border border-danger" 
                     style="border-width: 2px;">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-percent text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 text-danger fw-bold" style="font-size: 1.1rem;">
                                🎉 Diskon {{ $item->discount_percentage }}% Aktif!
                            </h6>
                            @if($item->discount_start && $item->discount_end)
                            <small class="text-muted">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($item->discount_start)->format('d M Y') }} 
                                - {{ \Carbon\Carbon::parse($item->discount_end)->format('d M Y') }}
                            </small>
                            @endif
                            <br>
                            <small class="text-danger fw-bold">
                                <i class="bi bi-clock me-1"></i>
                                Berakhir dalam {{ \Carbon\Carbon::today()->diffInDays($item->discount_end) }} hari
                            </small>
                        </div>
                    </div>
                </div>
                @endif

                <!-- ✅ CONTOH PERHITUNGAN (UPDATED WITH DISCOUNT) -->
                <div class="mt-3 p-3 bg-light rounded" style="border: 2px solid #e0e0e0;">
                    <h6 class="mb-2 fw-bold">📊 Contoh Perhitungan (3 Hari):</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.9rem;">
                        <tr>
                            <td class="text-muted">Sewa 3 hari:</td>
                            <td class="text-end">
                                @if($hasActiveDiscount)
                                    <span class="text-decoration-line-through text-muted">
                                        Rp {{ number_format($item->rental_price * 3, 0, ',', '.') }}
                                    </span>
                                    <br>
                                    <span class="text-primary fw-bold">
                                        Rp {{ number_format($discountedPrice * 3, 0, ',', '.') }}
                                    </span>
                                @else
                                    Rp {{ number_format($item->rental_price * 3, 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                        @if($hasActiveDiscount)
                        <tr>
                            <td class="text-success fw-bold">Total Hemat:</td>
                            <td class="text-end text-success fw-bold">
                                - Rp {{ number_format($savings * 3, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td class="text-muted">Deposit:</td>
                            <td class="text-end">Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="border-top" style="border-width: 2px !important;">
                            <td class="text-muted fw-bold">Total Bayar:</td>
                            <td class="text-end fw-bold text-primary" style="font-size: 1.1rem;">
                                Rp {{ number_format(($hasActiveDiscount ? $discountedPrice * 3 : $item->rental_price * 3) + ($item->deposit ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Item Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi Alat</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 150px;">Kode Alat</td>
                        <td class="fw-bold">{{ $item->code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>
                            <a href="{{ route('categories.show', $item->category->id) }}" class="text-decoration-none">
                                <span class="badge bg-info">{{ $item->category->name ?? '-' }}</span>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kondisi</td>
                        <td>
                            @php
                                $conditionBadge = [
                                    'baik' => 'bg-success',
                                    'rusak_ringan' => 'bg-warning',
                                    'rusak_berat' => 'bg-danger'
                                ][$item->condition] ?? 'bg-secondary';
                                
                                $conditionLabel = [
                                    'baik' => 'Baik',
                                    'rusak_ringan' => 'Rusak Ringan',
                                    'rusak_berat' => 'Rusak Berat'
                                ][$item->condition] ?? $item->condition;
                            @endphp
                            <span class="badge {{ $conditionBadge }}">{{ $conditionLabel }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $item->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diupdate</td>
                        <td>{{ $item->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Delete Button (Admin Only) -->
        @if(Auth::user()->isAdmin())
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('items.destroy', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash me-2"></i>Hapus Alat
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column - Borrowing History & Actions -->
    <div class="col-lg-7">
        <!-- Borrowing Statistics -->
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Total Pinjam</h6>
                        <h3 class="mb-0">{{ $item->borrowings()->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Sedang Dipinjam</h6>
                        <h3 class="mb-0">{{ $item->borrowings()->where('status', 'approved')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Sudah Kembali</h6>
                        <h3 class="mb-0">{{ $item->borrowings()->where('status', 'returned')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Borrowings -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Sedang Dipinjam
                    </h6>
                    @if($item->stock_available < $item->stock_total)
                        <span class="badge bg-warning text-dark">
                            {{ $item->stock_total - $item->stock_available }} Alat Dipinjam
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $currentBorrowings = $item->borrowings()
                        ->where('status', 'approved')
                        ->with('user')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($currentBorrowings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentBorrowings as $borrowing)
                            @php
                                $isOverdue = \Carbon\Carbon::parse($borrowing->return_date)->isPast();
                            @endphp
                            <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                                <td class="ps-4">
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
                                <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                                    @if($isOverdue)
                                        <br><small class="text-danger fw-bold">
                                            <i class="bi bi-exclamation-triangle"></i> Terlambat
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">Disetujui</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-check-circle text-success fs-1"></i>
                    <p class="text-muted mt-2 mb-0">Tidak ada peminjaman aktif</p>
                    <small class="text-muted">Semua alat tersedia untuk dipinjam</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Recent Borrowing History -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-list-ul me-2"></i>Riwayat Peminjaman (5 Terakhir)
                    </h6>
                    <a href="{{ route('borrowings.index') }}?item_id={{ $item->id }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @php
                    $recentBorrowings = $item->borrowings()
                        ->with('user', 'approvedBy')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentBorrowings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Disetujui</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $borrowing)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-2" 
                                             style="width: 35px; height: 35px;">
                                            {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $borrowing->user->name ?? '-' }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = [
                                            'pending' => 'bg-warning',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'returned' => 'bg-info',
                                        ][$borrowing->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                                <td>{{ $borrowing->approvedBy->name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada riwayat peminjaman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(Auth::user()->isAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('items.destroy', $item->id) }}" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="modal-header">
                    <h5 class="modal-title">Hapus Alat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $hasActiveBorrowings = $item->borrowings()->where('status', 'approved')->count() > 0;
                    @endphp
                    @if($hasActiveBorrowings)
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Tidak dapat menghapus!</strong> Alat ini masih memiliki peminjaman aktif.
                        </div>
                    @else
                        <p>Apakah Anda yakin ingin menghapus alat <strong>{{ $item->name }}</strong>?</p>
                        <p class="text-danger small">
                            <i class="bi bi-info-circle me-1"></i>
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    @if(!$hasActiveBorrowings)
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- CSS Animation for Discount Badge -->
@push('styles')
<style>
@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
        opacity: 1; 
    }
    50% { 
        transform: scale(1.05); 
        opacity: 0.85; 
    }
}
</style>
@endpush
@endsection