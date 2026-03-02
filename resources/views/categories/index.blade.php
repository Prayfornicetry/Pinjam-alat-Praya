@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-folder me-2 text-primary"></i>Kategori Alat
        </h4>
        <p class="text-muted mb-0">Kelola kategori untuk pengelompokan alat</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
    </a>
</div>

<!-- Flash Messages -->
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
        <form action="{{ route('categories.index') }}" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Cari kategori..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
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

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Total Kategori</h6>
                        <h3 class="mb-0">{{ $categories->total() }}</h3>
                    </div>
                    <i class="bi bi-folder fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Kategori Aktif</h6>
                        <h3 class="mb-0">{{ $categories->where('is_active', true)->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Total Alat Terkategori</h6>
                        <h3 class="mb-0">{{ $categories->sum('items_count') }}</h3>
                    </div>
                    <i class="bi bi-box-seam fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">Daftar Kategori</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Alat</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-secondary">#{{ $category->id }}</span>
                        </td>
                        <td>
                            <h6 class="mb-0 fw-bold">{{ $category->name }}</h6>
                        </td>
                        <td>
                            <code class="bg-light px-2 py-1 rounded">{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="text-muted">{{ Str::limit($category->description ?? '-', 40) }}</span>
                        </td>
                        <td>
                            @if($category->items_count > 0)
                                <a href="{{ route('items.index') }}?category={{ $category->id }}" 
                                   class="badge bg-info text-decoration-none">
                                    {{ $category->items_count }} Alat
                                </a>
                            @else
                                <span class="badge bg-secondary">0 Alat</span>
                            @endif
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="bi bi-x-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <small class="text-muted">{{ $category->created_at->format('d M Y') }}</small>
                        </td>
                        <td class="text-end pe-4">
                            {{-- ✅ TOMBOL AKSI LANGSUNG (BUKAN DROPDOWN) --}}
                            <div class="d-flex gap-1 justify-content-end">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('categories.show', $category->id) }}" 
                                   class="btn btn-sm btn-info text-white" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                {{-- Tombol Edit --}}
                                <a href="{{ route('categories.edit', $category->id) }}" 
                                   class="btn btn-sm btn-warning text-white" 
                                   title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                
                                {{-- Tombol Hapus --}}
                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                            <i class="bi bi-folder-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada kategori</p>
                            <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $categories->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection