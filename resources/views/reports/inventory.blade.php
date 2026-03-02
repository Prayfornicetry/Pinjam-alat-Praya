@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-box-seam me-2 text-primary"></i>Laporan Stok Alat
        </h4>
        <p class="text-muted mb-0">Inventaris dan ketersediaan alat</p>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer me-2"></i>Cetak
        </button>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center">
                <h6 class="mb-0">Total Jenis Alat</h6>
                <h3 class="mb-0">{{ $totalItems }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center">
                <h6 class="mb-0">Total Stok</h6>
                <h3 class="mb-0">{{ $totalStock }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center">
                <h6 class="mb-0">Stok Tersedia</h6>
                <h3 class="mb-0">{{ $availableStock }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body text-center">
                <h6 class="mb-0">Stok Menipis</h6>
                <h3 class="mb-0">{{ $lowStock }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">Daftar Alat ({{ $items->count() }} Item)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Kode</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Stok Total</th>
                        <th>Tersedia</th>
                        <th>Dipinjam</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="{{ $item->stock_available <= 2 ? 'table-warning' : '' }}">
                        <td class="ps-4"><span class="badge bg-secondary">{{ $item->code }}</span></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? '-' }}</td>
                        <td>{{ $item->stock_total }}</td>
                        <td>
                            <span class="fw-bold {{ $item->stock_available <= 2 ? 'text-danger' : 'text-success' }}">
                                {{ $item->stock_available }}
                            </span>
                        </td>
                        <td>{{ $item->stock_total - $item->stock_available }}</td>
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
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1"></i>
                            <p class="mb-0 mt-2">Belum ada data alat</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection