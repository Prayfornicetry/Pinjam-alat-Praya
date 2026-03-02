@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-tools me-2 text-primary"></i>Data Alat
        </h4>
        <p class="text-muted mb-0">Lihat katalog alat yang tersedia untuk dipinjam</p>
    </div>
    <a href="{{ route('borrowing.request.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
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
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="position-relative">
                @if($item->image && file_exists(public_path('storage/' . $item->image)))
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="card-img-top" 
                         style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" 
                         style="height: 200px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                    </div>
                @endif
                
                @if($item->stock_available <= 0)
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-danger">Stok Habis</span>
                </div>
                @elseif($item->stock_available <= 2)
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-warning text-dark">Stok Tersisa {{ $item->stock_available }}</span>
                </div>
                @else
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-success">Tersedia</span>
                </div>
                @endif
            </div>
            
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0 text-truncate">{{ $item->name }}</h6>
                    <small class="text-muted">{{ $item->code }}</small>
                </div>
                
                <p class="card-text text-muted small mb-2">
                    {{ Str::limit($item->description, 60) ?? 'Tidak ada deskripsi' }}
                </p>
                
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
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <small class="text-muted">
                        <i class="bi bi-box-seam me-1"></i>
                        Stok: <strong>{{ $item->stock_available }}/{{ $item->stock_total }}</strong>
                    </small>
                </div>
                
                <div class="d-grid">
                    <a href="{{ route('items.user.show', $item->id) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-eye me-1"></i> Lihat Detail
                    </a>
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
@endsection