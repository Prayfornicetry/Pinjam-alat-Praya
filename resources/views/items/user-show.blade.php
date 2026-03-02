@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-box-seam me-2 text-primary"></i>Detail Alat
        </h4>
        <p class="text-muted mb-0">Informasi lengkap alat</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('items.user.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
        @if($item->stock_available > 0)
        <a href="{{ route('borrowing.request.create') }}?item_id={{ $item->id }}" 
           class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman
        </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Item Image & Info -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                @if($item->image && file_exists(public_path('storage/' . $item->image)))
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="img-fluid rounded mb-3" 
                         style="width: 100%; max-height: 400px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                         style="height: 300px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 6rem;"></i>
                    </div>
                @endif
                
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">{{ $item->name }}</h5>
                    <span class="badge bg-secondary">{{ $item->code }}</span>
                </div>
                
                <div class="d-flex gap-2 mb-3">
                    <span class="badge bg-info">{{ $item->category->name ?? '-' }}</span>
                    
                    @php
                        $conditionBadge = [
                            'baik' => 'bg-success',
                            'rusak_ringan' => 'bg-warning',
                            'rusak_berat' => 'bg-danger'
                        ][$item->condition] ?? 'bg-secondary';
                    @endphp
                    <span class="badge {{ $conditionBadge }}">
                        {{ ucfirst(str_replace('_', ' ', $item->condition)) }}
                    </span>
                    
                    @if($item->is_active)
                        <span class="badge bg-success">Aktif</span>
                    @else
                        <span class="badge bg-danger">Nonaktif</span>
                    @endif
                </div>
                
                @if($item->stock_available > 0)
                <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Tersedia untuk dipinjam!</strong>
                    <br>
                    <small>Stok tersedia: {{ $item->stock_available }}/{{ $item->stock_total }}</small>
                </div>
                @else
                <div class="alert alert-danger mb-0">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>Stok habis</strong>
                    <br>
                    <small>Silakan cek kembali nanti</small>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row g-3">
            <div class="col-6">
                <div class="card border-0 shadow-sm bg-primary text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75 small">Total Stok</h6>
                        <h3 class="mb-0">{{ $item->stock_total }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card border-0 shadow-sm bg-success text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75 small">Tersedia</h6>
                        <h3 class="mb-0">{{ $item->stock_available }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Item Details -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi Detail</h6>
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
                        <td>{{ $item->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kondisi</td>
                        <td>
                            @php
                                $conditionLabel = [
                                    'baik' => 'Baik',
                                    'rusak_ringan' => 'Rusak Ringan',
                                    'rusak_berat' => 'Rusak Berat'
                                ][$item->condition] ?? $item->condition;
                            @endphp
                            {{ $conditionLabel }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Stok</td>
                        <td>{{ $item->stock_total }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Stok Tersedia</td>
                        <td>
                            <strong class="{{ $item->stock_available > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $item->stock_available }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $item->description ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- My Borrowing History for this Item -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman Saya
                </h6>
            </div>
            <div class="card-body p-0">
                @if($item->borrowings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($item->borrowings as $borrowing)
                            <tr>
                                <td class="ps-4">
                                    {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                                </td>
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
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-3 text-muted"></i>
                    <p class="text-muted small mt-2 mb-0">Anda belum pernah meminjam alat ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection