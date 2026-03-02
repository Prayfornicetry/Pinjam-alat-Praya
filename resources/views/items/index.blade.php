@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-box-seam me-2 text-primary"></i>Data Alat / Inventaris
        </h4>
        <p class="text-muted mb-0">Kelola semua peralatan yang tersedia</p>
    </div>
    <a href="{{ route('items.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Alat Baru
    </a>
</div>

<!-- Flash Message -->
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

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('items.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="🔍 Cari nama alat..." value="{{ request('search') }}">
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

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">Daftar Alat ({{ $items->total() }} Total)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Gambar</th>
                        <th>Kode</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="ps-4">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}" 
                                     alt="{{ $item->name }}" 
                                     class="rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="bi bi-image text-muted fs-4"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $item->code }}</span>
                        </td>
                        <td>
                            <h6 class="mb-0">{{ $item->name }}</h6>
                            <small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                        </td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="fw-bold">{{ $item->stock_available }}</span>
                                <span class="text-muted mx-1">/</span>
                                <small class="text-muted">{{ $item->stock_total }}</small>
                            </div>
                            @if($item->stock_available <= 2)
                                <span class="badge bg-danger mt-1">Stok Rendah</span>
                            @endif
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
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            {{-- ✅ TOMBOL AKSI LANGSUNG (BUKAN DROPDOWN) --}}
                            <div class="d-flex gap-1 justify-content-end">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('items.show', $item->id) }}" 
                                   class="btn btn-sm btn-info text-white" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                {{-- Tombol Edit --}}
                                <a href="{{ route('items.edit', $item->id) }}" 
                                   class="btn btn-sm btn-warning text-white" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Tombol Hapus --}}
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
                        <td colspan="8" class="text-center py-5">
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
    <div class="card-footer bg-white border-0 py-3">
        {{ $items->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection