@extends('layouts.app')

@section('title', 'Katalog Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Katalog Alat
        </h4>
        <p class="text-muted mb-0">Lihat katalog alat yang tersedia untuk dipinjam</p>
    </div>
    <a href="{{ route('borrowing.request.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman
    </a>
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

<!-- Search & Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('items.user.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Cari nama alat..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="condition" class="form-select">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="rusak_ringan" {{ request('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ request('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Items Grid -->
<div class="row g-4">
    @forelse($items as $item)
    <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100 position-relative">
            
            <!-- ✅ STOCK STATUS BADGE -->
            @if($item->stock_available <= 0)
                <div class="position-absolute top-0 end-0 m-2 z-3">
                    <span class="badge bg-danger px-3 py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-x-circle me-1"></i>Stok Habis
                    </span>
                </div>
            @elseif($item->stock_available <= 2)
                <div class="position-absolute top-0 end-0 m-2 z-3">
                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-exclamation-triangle me-1"></i>Stok Tersisa {{ $item->stock_available }}
                    </span>
                </div>
            @else
                <div class="position-absolute top-0 end-0 m-2 z-3">
                    <span class="badge bg-success px-3 py-2" style="font-size: 0.85rem;">
                        <i class="bi bi-check-circle me-1"></i>Tersedia
                    </span>
                </div>
            @endif

            <!-- ✅ DISCOUNT BADGE -->
            @php
                $hasActiveDiscount = $item->has_discount && $item->discount_percentage > 0 && $item->hasActiveDiscount();
                $discountedPrice = $hasActiveDiscount 
                    ? $item->rental_price - ($item->rental_price * $item->discount_percentage / 100)
                    : $item->rental_price;
                $discountedMemberPrice = $hasActiveDiscount 
                    ? $item->member_price - ($item->member_price * $item->discount_percentage / 100)
                    : $item->member_price;
            @endphp

            @if($hasActiveDiscount)
            <div class="position-absolute top-0 start-0 m-2 z-3">
                <span class="badge bg-danger px-3 py-2" style="font-size: 0.85rem; animation: pulse 2s infinite;">
                    <i class="bi bi-percent me-1"></i>{{ $item->discount_percentage }}% OFF
                </span>
            </div>
            @endif

            <!-- Item Image -->
            <div class="card-img-top position-relative" style="height: 200px; overflow: hidden;">
                @if($item->image && file_exists(public_path('storage/' . $item->image)))
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="w-100 h-100" 
                         style="object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center w-100 h-100">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <!-- Title & Code -->
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 text-truncate" style="max-width: 70%;">
                        {{ $item->name }}
                    </h6>
                    <small class="text-muted">{{ $item->code }}</small>
                </div>

                <!-- Category & Condition -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge bg-info">{{ $item->category->name ?? '-' }}</span>
                    @php
                        $conditionBadge = [
                            'baik' => 'bg-success',
                            'rusak_ringan' => 'bg-warning',
                            'rusak_berat' => 'bg-danger'
                        ][$item->condition] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $conditionBadge }} small">
                        {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                    </span>
                </div>

                <!-- Stock Info -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        <i class="bi bi-box-seam me-1"></i>
                        Stok: <strong>{{ $item->stock_available }}/{{ $item->stock_total }}</strong>
                    </small>
                </div>

                <!-- ✅ PRICE INFORMATION CARD -->
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2 px-3">
                        <h6 class="mb-2 fw-bold text-primary" style="font-size: 0.85rem;">
                            <i class="bi bi-currency-dollar me-1"></i>Informasi Harga
                        </h6>
                        
                        <!-- Rental Price -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Harga Sewa:</small>
                            @if($hasActiveDiscount)
                                <div>
                                    <span class="text-decoration-line-through text-muted small">
                                        Rp {{ number_format($item->rental_price, 0, ',', '.') }}
                                    </span>
                                    <br>
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($discountedPrice, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted">/hari</small>
                                </div>
                            @else
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($item->rental_price ?? 0, 0, ',', '.') }}
                                    <small class="text-muted">/hari</small>
                                </span>
                            @endif
                        </div>

                        <!-- Member Price -->
                        @if($item->member_price > 0)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Harga Member:</small>
                            @if($hasActiveDiscount)
                                <div>
                                    <span class="text-decoration-line-through text-muted small">
                                        Rp {{ number_format($item->member_price, 0, ',', '.') }}
                                    </span>
                                    <br>
                                    <span class="fw-bold text-success">
                                        Rp {{ number_format($discountedMemberPrice, 0, ',', '.') }}
                                    </span>
                                    <small class="text-muted">/hari</small>
                                </div>
                            @else
                                <span class="fw-bold text-success">
                                    Rp {{ number_format($item->member_price, 0, ',', '.') }}
                                    <small class="text-muted">/hari</small>
                                </span>
                            @endif
                        </div>
                        @endif

                        <!-- Late Fee -->
                        @if($item->late_fee > 0)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>Denda:
                            </small>
                            <span class="fw-bold text-warning">
                                Rp {{ number_format($item->late_fee, 0, ',', '.') }}
                                <small class="text-muted">/hari</small>
                            </span>
                        </div>
                        @endif

                        <!-- Deposit -->
                        @if($item->deposit > 0)
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">
                                <i class="bi bi-shield-check text-info me-1"></i>Deposit:
                            </small>
                            <span class="fw-bold text-info">
                                Rp {{ number_format($item->deposit, 0, ',', '.') }}
                            </span>
                        </div>
                        @endif

                        <!-- Discount Info -->
                        @if($hasActiveDiscount)
                        <div class="mt-2 p-2 bg-danger bg-opacity-10 rounded">
                            <small class="text-danger fw-bold">
                                <i class="bi bi-percent me-1"></i>Diskon {{ $item->discount_percentage }}% Aktif!
                            </small>
                            @if($item->discount_end)
                            <br>
                            <small class="text-muted">
                                Hingga {{ \Carbon\Carbon::parse($item->discount_end)->format('d M Y') }}
                            </small>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <p class="card-text text-muted small mb-3">
                    {{ Str::limit($item->description, 60) ?? 'Tidak ada deskripsi' }}
                </p>

                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="{{ route('items.user.show', $item->id) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </a>
                    
                    @if($item->stock_available > 0 && $item->is_active)
                        <a href="{{ route('borrowing.request.create') }}?item_id={{ $item->id }}" 
                           class="btn btn-primary btn-sm">
                            <i class="bi bi-cart-plus me-1"></i> Pinjam Sekarang
                        </a>
                    @else
                        <button class="btn btn-secondary btn-sm" disabled>
                            <i class="bi bi-slash-circle me-1"></i> Stok Habis
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="text-muted mt-2 mb-0">Belum ada alat tersedia</p>
                <small class="text-muted">Silakan cek kembali nanti</small>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($items->hasPages())
<div class="mt-4">
    {{ $items->links('pagination::bootstrap-5') }}
</div>
@endif

<!-- Price Info Legend -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">💡 Info Harga</h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-currency-dollar text-primary me-2 fs-5"></i>
                    <div>
                        <small class="text-muted d-block">Harga Sewa</small>
                        <small>Biaya per hari peminjaman</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle text-warning me-2 fs-5"></i>
                    <div>
                        <small class="text-muted d-block">Denda</small>
                        <small>Biaya jika terlambat</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check text-info me-2 fs-5"></i>
                    <div>
                        <small class="text-muted d-block">Deposit</small>
                        <small>Jaminan (dikembalikan)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-percent text-danger me-2 fs-5"></i>
                    <div>
                        <small class="text-muted d-block">Diskon</small>
                        <small>Potongan harga spesial</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

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

@push('scripts')
<script>
// Auto-submit filter on change
document.addEventListener('DOMContentLoaded', function() {
    const filters = document.querySelectorAll('select[name="category"], select[name="condition"]');
    filters.forEach(filter => {
        filter.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
});
</script>
@endpush