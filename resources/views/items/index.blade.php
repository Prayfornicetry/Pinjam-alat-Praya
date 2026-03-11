@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-tools me-2 text-primary"></i>Data Alat / Inventaris
        </h4>
        <p class="text-muted mb-0">Kelola semua peralatan yang tersedia</p>
    </div>
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Alat Baru
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
        <form action="{{ route('items.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Cari nama alat..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach(\App\Models\Category::all() as $cat)
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

<!-- Items Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">📋 Daftar Alat ({{ $items->total() }} Total)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="15%">Gambar</th>
                        <th width="10%">Kode</th>
                        <th width="20%">Nama Alat</th>
                        <th width="10%">Kategori</th>
                        <th width="8%">Stok</th>
                        <th width="12%">Harga Sewa</th> <!-- ✅ TAMBAH KOLOM INI -->
                        <th width="8%">Kondisi</th>
                        <th width="7%">Status</th>
                        <th class="text-end pe-4" width="10%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4">{{ $items->firstItem() + $loop->index }}</td>
                        <td>
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}" 
                                     alt="{{ $item->name }}" 
                                     class="rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="bi bi-box-seam text-muted fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold">{{ $item->code }}</span>
                        </td>
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $item->name }}</h6>
                                <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $item->category->name ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold">{{ $item->stock_available }}</span>
                                <small class="text-muted">/ {{ $item->stock_total }}</small>
                                @if($item->stock_available <= 2 && $item->stock_available > 0)
                                    <span class="badge bg-warning text-dark small mt-1">Stok Rendah</span>
                                @elseif($item->stock_available <= 0)
                                    <span class="badge bg-danger small mt-1">Habis</span>
                                @endif
                            </div>
                        </td>
                        <!-- ✅ KOLOM HARGA - TAMBAHKAN INI -->
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-primary">
                                    Rp {{ number_format($item->rental_price ?? 0, 0, ',', '.') }}
                                </span>
                                @if($item->member_price > 0)
                                    <small class="text-muted">
                                        Member: Rp {{ number_format($item->member_price, 0, ',', '.') }}
                                    </small>
                                @endif
                                @if($item->hasActiveDiscount())
                                    <span class="badge bg-danger small mt-1">
                                        Diskon {{ $item->discount_percentage }}%
                                    </span>
                                @endif
                                @if($item->late_fee > 0)
                                    <small class="text-warning mt-1">
                                        <i class="bi bi-clock"></i> Denda: Rp {{ number_format($item->late_fee, 0, ',', '.') }}/hari
                                    </small>
                                @endif
                            </div>
                        </td>
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
                        <td>
                            @if($item->is_active)
                                <span class="badge bg-success">✅ Aktif</span>
                            @else
                                <span class="badge bg-danger">❌ Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('items.show', $item->id) }}" 
                                   class="btn btn-sm btn-info text-white" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                <a href="{{ route('items.edit', $item->id) }}" 
                                   class="btn btn-sm btn-warning text-white" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada data alat</p>
                            <a href="{{ route('items.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Alat Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($items->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

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