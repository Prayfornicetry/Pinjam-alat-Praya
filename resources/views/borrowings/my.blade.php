@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Peminjaman Saya
        </h4>
        <p class="text-muted mb-0">Riwayat dan status peminjaman Anda</p>
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

<!-- Stats Cards -->
@php
    $total = \App\Models\Borrowing::where('user_id', Auth::id())->count();
    $pending = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'pending')->count();
    $approved = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'approved')->count();
    $returned = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'returned')->count();
    
    // ✅ Hitung yang overdue/terlambat
    $overdue = \App\Models\Borrowing::where('user_id', Auth::id())
        ->where('status', 'approved')
        ->where('return_date', '<', \Carbon\Carbon::today())
        ->count();
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75 small">Total</h6>
                <h3 class="mb-0">{{ $total }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-warning text-white h-100">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75 small">Pending</h6>
                <h3 class="mb-0">{{ $pending }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75 small">Disetujui</h6>
                <h3 class="mb-0">{{ $approved }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card border-0 shadow-sm bg-info text-white h-100">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75 small">Selesai</h6>
                <h3 class="mb-0">{{ $returned }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- ✅ OVERDUE ALERT -->
@if($overdue > 0)
<div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
        <div class="flex-grow-1">
            <h6 class="mb-1 fw-bold">⚠️ Anda memiliki {{ $overdue }} peminjaman yang terlambat!</h6>
            <p class="mb-0 small">Segera kembalikan alat untuk menghindari denda keterlambatan.</p>
        </div>
        <a href="{{ route('borrowings.my') }}?status=approved" class="btn btn-danger btn-sm">
            Lihat Detail <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('borrowings.my') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Cari nama alat..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>✓ Dikembalikan</option>
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

<!-- Borrowings Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <h6 class="mb-0 fw-bold">📋 Daftar Peminjaman ({{ $borrowings->total() }})</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4" width="5%">#</th>
                        <th width="25%">Alat</th>
                        <th width="12%">Tgl Pinjam</th>
                        <th width="12%">Tgl Kembali</th>
                        <th width="10%">Status</th>
                        <th width="15%">Jatuh Tempo</th>
                        <th width="10%">Denda</th>
                        <th class="text-end pe-4" width="11%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                    @php
                        $badgeClass = [
                            'pending' => 'bg-warning text-dark',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'returned' => 'bg-info text-dark',
                        ][$borrowing->status] ?? 'bg-secondary';
                        
                        $statusLabel = [
                            'pending' => '⏳ Pending',
                            'approved' => '✅ Disetujui',
                            'rejected' => '❌ Ditolak',
                            'returned' => '✓ Dikembalikan',
                        ][$borrowing->status] ?? $borrowing->status;
                        
                        // ✅ Cek apakah sudah waktunya kembalikan atau terlambat
                        $returnDate = \Carbon\Carbon::parse($borrowing->return_date);
                        $today = \Carbon\Carbon::today();
                        $isOverdue = $borrowing->status === 'approved' && $returnDate->lt($today);
                        $isDueToday = $borrowing->status === 'approved' && $returnDate->isToday();
                        $isUpcoming = $borrowing->status === 'approved' && $returnDate->diffInDays($today) <= 3 && $returnDate->gt($today);
                        
                        // ✅ Hitung denda jika terlambat
                        $lateFee = 0;
                        if ($isOverdue && $borrowing->item) {
                            $lateDays = $returnDate->diffInDays($today);
                            $lateFee = ($borrowing->item->late_fee ?? 0) * $lateDays;
                        }
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-danger' : '' }} {{ $isDueToday ? 'table-warning' : '' }}">
                        <td class="ps-4">{{ $borrowings->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($borrowing->item && $borrowing->item->image && file_exists(public_path('storage/' . $borrowing->item->image)))
                                    <img src="{{ asset('storage/' . $borrowing->item->image) }}" 
                                         alt="{{ $borrowing->item->name }}" 
                                         class="rounded me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="bi bi-box-seam text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $borrowing->item->name ?? '-' }}</h6>
                                    <small class="text-muted">{{ $borrowing->item->code ?? '-' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                            @if($isOverdue)
                                <br><small class="text-danger fw-bold">
                                    <i class="bi bi-exclamation-triangle"></i> Terlambat
                                </small>
                            @elseif($isDueToday)
                                <br><small class="text-warning fw-bold">
                                    <i class="bi bi-clock"></i> Hari Ini
                                </small>
                            @elseif($isUpcoming)
                                <br><small class="text-info">
                                    <i class="bi bi-info-circle"></i> {{ $returnDate->diffInDays($today) }} hari lagi
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            @if($isOverdue)
                                <span class="text-danger fw-bold">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    {{ $returnDate->diffInDays($today) }} hari
                                </span>
                            @elseif($isDueToday)
                                <span class="text-warning fw-bold">
                                    <i class="bi bi-clock me-1"></i>Hari Ini
                                </span>
                            @elseif($isUpcoming)
                                <span class="text-info">
                                    {{ $returnDate->diffInDays($today) }} hari
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($lateFee > 0)
                                <span class="text-danger fw-bold">
                                    Rp {{ number_format($lateFee, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- ✅ Tombol Detail (Semua Status) -->
                                <a href="{{ route('borrowings.my.show', $borrowing->id) }}" 
                                   class="btn btn-sm btn-info text-white" 
                                   title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                <!-- ✅ Tombol Kembalikan (Hanya jika approved & sudah waktunya) -->
                                @if($borrowing->status === 'approved' && ($isOverdue || $isDueToday || $isUpcoming))
                                <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" 
                                            class="btn btn-sm btn-success" 
                                            title="Kembalikan"
                                            onclick="return confirm('Konfirmasi pengembalian alat? Pastikan alat dalam kondisi baik.')">
                                        <i class="bi bi-arrow-return-left"></i>
                                    </button>
                                </form>
                                @endif
                                
                                <!-- ✅ Tombol Cancel (Hanya jika pending) -->
                                @if($borrowing->status === 'pending')
                                <form action="{{ route('borrowings.destroy', $borrowing->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-sm btn-danger" 
                                            title="Batalkan"
                                            onclick="return confirm('Yakin ingin membatalkan peminjaman ini?')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada peminjaman</p>
                            <a href="{{ route('borrowing.request.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($borrowings->hasPages())
    <div class="card-footer bg-white border-0 py-3">
        {{ $borrowings->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Info Cards -->
<div class="row g-3 mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-2">📌 Cara Mengembalikan Alat:</h6>
                <ol class="mb-0 small">
                    <li>Pastikan alat dalam kondisi baik</li>
                    <li>Klik tombol <strong>"Kembalikan"</strong> saat sudah waktunya</li>
                    <li>Serahkan alat kepada admin/staff</li>
                    <li>Tunggu konfirmasi pengembalian</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-2">⚠️ Informasi Denda:</h6>
                <ul class="mb-0 small">
                    <li>Denda dihitung per hari keterlambatan</li>
                    <li>Bayar denda sebelum peminjaman berikutnya</li>
                    <li>Deposit akan dikembalikan setelah alat dicek</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection