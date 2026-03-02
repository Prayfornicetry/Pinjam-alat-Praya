@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-box-seam me-2 text-primary"></i>Detail Alat
        </h4>
        <p class="text-muted mb-0">Informasi lengkap inventaris alat</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
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

<div class="row">
    <!-- Left Column - Item Info -->
    <div class="col-lg-5">
        <!-- Item Image -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
                @if($item->image && file_exists(public_path('storage/' . $item->image)))
                    <img src="{{ asset('storage/' . $item->image) }}" 
                         alt="{{ $item->name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 300px; object-fit: cover;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                         style="height: 300px;">
                        <i class="bi bi-box-seam text-muted" style="font-size: 5rem;"></i>
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
        
        <!-- Item Information -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi Alat</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">Kode Alat</td>
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
@endsection