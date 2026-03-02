@extends('layouts.app')

@section('title', 'Detail Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-folder me-2 text-primary"></i>{{ $category->name }}
        </h4>
        <p class="text-muted mb-0">Detail informasi kategori</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">ID</td>
                        <td class="fw-bold">#{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td class="fw-bold">{{ $category->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Slug</td>
                        <td><code>{{ $category->slug }}</code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Deskripsi</td>
                        <td>{{ $category->description ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            @if($category->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $category->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diupdate</td>
                        <td>{{ $category->updated_at->format('d M Y, H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Alat dalam Kategori Ini ({{ $category->items->count() }})
                </h6>
            </div>
            <div class="card-body p-0">
                @if($category->items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Kode</th>
                                <th>Nama Alat</th>
                                <th>Stok</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->items->take(10) as $item)
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-secondary">{{ $item->code }}</span>
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->stock_available }} / {{ $item->stock_total }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->condition === 'baik' ? 'success' : ($item->condition === 'rusak_ringan' ? 'warning' : 'danger') }}">
                                        {{ $item->condition }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">
                                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($category->items->count() > 10)
                <div class="text-center p-3">
                    <a href="{{ route('items.index') }}?category={{ $category->id }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua Alat <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada alat dalam kategori ini</p>
                    <a href="{{ route('items.create') }}" class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Alat
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection