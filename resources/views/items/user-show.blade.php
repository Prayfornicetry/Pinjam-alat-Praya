@extends('layouts.app')

@section('title', 'Detail Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-eye me-2 text-primary"></i>Detail Alat
        </h4>
        <p class="text-muted mb-0">Informasi lengkap alat untuk peminjaman</p>
    </div>
    <div class="d-flex gap-2">
        @if($item->stock_available > 0 && $item->is_active)
        <a href="{{ route('borrowing.request.create') }}?item_id={{ $item->id }}" class="btn btn-primary">
            <i class="bi bi-cart-plus me-2"></i>Ajukan Peminjaman
        </a>
        @endif
        <a href="{{ route('items.user.index') }}" class="btn btn-outline-secondary">
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
                        <span class="badge bg-danger px-3 py-2">Stok Habis</span>
                    </div>
                @elseif($item->stock_available <= 2)
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-warning text-dark px-3 py-2">Stok Tersisa {{ $item->stock_available }}</span>
                    </div>
                @else
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success px-3 py-2">Tersedia</span>
                    </div>
                @endif
                
                <!-- Discount Badge -->
                @if($item->hasActiveDiscount())
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge bg-danger px-3 py-2">
                            <i class="bi bi-percent me-1"></i>{{ $item->discount_percentage }}% OFF
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-3 mb-3">
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

        <!-- ✅ TAMBAHAN: Price Information Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💰 Informasi Harga</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Harga Sewa</small>
                            <h5 class="mb-0 text-primary fw-bold">
                                Rp {{ number_format($item->rental_price ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/hari</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Harga Member</small>
                            <h5 class="mb-0 text-success fw-bold">
                                Rp {{ number_format($item->member_price ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/hari</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">
                                <i class="bi bi-exclamation-triangle text-warning me-1"></i>Denda
                            </small>
                            <h5 class="mb-0 text-warning fw-bold">
                                Rp {{ number_format($item->late_fee ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/hari</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">
                                <i class="bi bi-shield-check text-info me-1"></i>Deposit
                            </small>
                            <h5 class="mb-0 text-info fw-bold">
                                Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }}
                            </h5>
                            <small class="text-muted">/pinjam</small>
                        </div>
                    </div>
                </div>

                <!-- Discount Info -->
                @if($item->hasActiveDiscount())
                <div class="mt-3 p-3 bg-danger bg-opacity-10 rounded border border-danger">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-percent text-danger me-2 fs-4"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-danger fw-bold">Diskon {{ $item->discount_percentage }}% Aktif!</h6>
                            @if($item->discount_end)
                            <small class="text-muted">
                                Hingga {{ \Carbon\Carbon::parse($item->discount_end)->format('d M Y') }}
                            </small>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <!-- Price Calculation Example -->
                <div class="mt-3 p-3 bg-light rounded">
                    <h6 class="mb-2 fw-bold">📊 Contoh Perhitungan:</h6>
                    <table class="table table-sm table-borderless mb-0" style="font-size: 0.85rem;">
                        <tr>
                            <td class="text-muted">Pinjam 3 hari:</td>
                            <td class="text-end">Rp {{ number_format(($item->rental_price ?? 0) * 3, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Deposit:</td>
                            <td class="text-end">Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @if($item->hasActiveDiscount())
                        <tr>
                            <td class="text-muted text-success">Diskon {{ $item->discount_percentage }}%:</td>
                            <td class="text-end text-success">- Rp {{ number_format((($item->rental_price ?? 0) * 3) * ($item->discount_percentage / 100), 0, ',', '.') }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="text-muted fw-bold">Total:</td>
                            <td class="text-end fw-bold text-primary">
                                Rp {{ number_format((($item->rental_price ?? 0) * 3) + ($item->deposit ?? 0), 0, ',', '.') }}
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
                        <td class="text-muted" width="40%">Kode Alat</td>
                        <td class="fw-bold">{{ $item->code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Nama</td>
                        <td class="fw-bold">{{ $item->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kategori</td>
                        <td>
                            <span class="badge bg-info">{{ $item->category->name ?? '-' }}</span>
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
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column - Borrowing Info & Actions -->
    <div class="col-lg-7">
        <!-- Borrow Action Card -->
        @if($item->stock_available > 0 && $item->is_active)
        <div class="card border-0 shadow-sm mb-4 bg-primary bg-gradient text-white">
            <div class="card-body py-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-2">🎯 Alat Tersedia untuk Dipinjam!</h5>
                        <p class="mb-0 opacity-75">Stok tersedia: {{ $item->stock_available }}/{{ $item->stock_total }}</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <a href="{{ route('borrowing.request.create') }}?item_id={{ $item->id }}" 
                           class="btn btn-light btn-lg">
                            <i class="bi bi-cart-plus me-2"></i>Pinjam Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm mb-4 bg-danger bg-gradient text-white">
            <div class="card-body py-4 text-center">
                <i class="bi bi-x-circle fs-1 mb-2"></i>
                <h5 class="mb-2">Stok Habis atau Tidak Aktif</h5>
                <p class="mb-0 opacity-75">Silakan cek kembali nanti atau pilih alat lain</p>
            </div>
        </div>
        @endif

        <!-- My Borrowing History for this Item -->
        <div class="card border-0 shadow-sm mb-4">
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
                                <th class="text-end pe-4">Aksi</th>
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
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'returned' => 'bg-info text-dark',
                                        ][$borrowing->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('borrowings.my.show', $borrowing->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-3 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Anda belum pernah meminjam alat ini</p>
                    @if($item->stock_available > 0 && $item->is_active)
                    <a href="{{ route('borrowing.request.create') }}?item_id={{ $item->id }}" 
                       class="btn btn-sm btn-primary mt-2">
                        <i class="bi bi-cart-plus me-1"></i>Jadikan yang Pertama!
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Borrowing Rules -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-info-circle me-2"></i>Aturan Peminjaman
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Maksimal Pinjam:</strong> 
                        {{ \App\Models\Setting::get('loan_max_days', 7) }} hari
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Max Alat per User:</strong> 
                        {{ \App\Models\Setting::get('loan_max_items', 3) }} alat
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Denda Keterlambatan:</strong> 
                        Rp {{ number_format($item->late_fee ?? 0, 0, ',', '.') }}/hari
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Deposit:</strong> 
                        Rp {{ number_format($item->deposit ?? 0, 0, ',', '.') }} (dikembalikan)
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Proses Approval:</strong> 1-2 hari kerja
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection