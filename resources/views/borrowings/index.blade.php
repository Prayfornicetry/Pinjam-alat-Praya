@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Data Peminjaman
        </h4>
        <p class="text-muted mb-0">Kelola semua transaksi peminjaman alat</p>
    </div>
    <a href="{{ route('borrowings.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Peminjaman Baru
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

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    @php
        $pending = \App\Models\Borrowing::where('status', 'pending')->count();
        $approved = \App\Models\Borrowing::where('status', 'approved')->count();
        $returned = \App\Models\Borrowing::where('status', 'returned')->count();
        $overdue = \App\Models\Borrowing::where('status', 'approved')
                    ->where('return_date', '<', \Carbon\Carbon::today())->count();
    @endphp
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Pending</h6>
                        <h3 class="mb-0">{{ $pending }}</h3>
                    </div>
                    <i class="bi bi-clock fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Disetujui</h6>
                        <h3 class="mb-0">{{ $approved }}</h3>
                    </div>
                    <i class="bi bi-check-circle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Dikembalikan</h6>
                        <h3 class="mb-0">{{ $returned }}</h3>
                    </div>
                    <i class="bi bi-arrow-return-left fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Terlambat</h6>
                        <h3 class="mb-0">{{ $overdue }}</h3>
                    </div>
                    <i class="bi bi-exclamation-triangle fs-1 opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('borrowings.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Cari peminjam atau alat..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="user_id" class="form-select">
                    <option value="">Semua Peminjam</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
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
        <h6 class="mb-0 fw-bold">Daftar Peminjaman</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                    @php
                        $isOverdue = $borrowing->status === 'approved' && 
                                    \Carbon\Carbon::parse($borrowing->return_date)->isPast();
                        
                        $badgeClass = [
                            'pending' => 'bg-warning',
                            'approved' => $isOverdue ? 'bg-danger' : 'bg-success',
                            'rejected' => 'bg-danger',
                            'returned' => 'bg-info',
                        ][$borrowing->status] ?? 'bg-secondary';
                        
                        $badgeLabel = [
                            'pending' => 'Pending',
                            'approved' => $isOverdue ? 'Terlambat' : 'Disetujui',
                            'rejected' => 'Ditolak',
                            'returned' => 'Dikembalikan',
                        ][$borrowing->status] ?? $borrowing->status;
                    @endphp
                    <tr class="{{ $isOverdue ? 'table-danger' : '' }}">
                        <td class="ps-4">
                            <span class="badge bg-secondary">#{{ $borrowing->id }}</span>
                        </td>
                        <td>
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
                        <td>
                            <div>
                                <h6 class="mb-0">{{ $borrowing->item->name ?? '-' }}</h6>
                                <small class="text-muted">{{ $borrowing->item->code ?? '-' }}</small>
                            </div>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}
                            @if($isOverdue)
                                <br><small class="text-danger fw-bold">
                                    {{ \Carbon\Carbon::parse($borrowing->return_date)->diffForHumans() }}
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }}">
                                {{ $badgeLabel }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex gap-1 justify-content-end">
                                <!-- Detail -->
                                <a href="{{ route('borrowings.show', $borrowing->id) }}" 
                                   class="btn btn-sm btn-info text-white" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
                                @if($borrowing->status === 'pending')
                                    <!-- Approve -->
                                    <form action="{{ route('borrowings.approve', $borrowing->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui"
                                                onclick="return confirm('Setujui peminjaman ini?')">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    
                                    <!-- Reject -->
                                    <button type="button" class="btn btn-sm btn-danger" title="Tolak"
                                            data-bs-toggle="modal" data-bs-target="#rejectModal{{ $borrowing->id }}">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                @endif
                                
                                @if($borrowing->status === 'approved')
                                    <!-- Return -->
                                    <form action="{{ route('borrowings.return', $borrowing->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary" title="Kembalikan"
                                                onclick="return confirm('Konfirmasi pengembalian alat?')">
                                            <i class="bi bi-arrow-return-left"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Modal Reject -->
                    @if($borrowing->status === 'pending')
                    <div class="modal fade" id="rejectModal{{ $borrowing->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('borrowings.reject', $borrowing->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Tolak Peminjaman</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Alasan Penolakan</label>
                                            <textarea name="rejection_reason" class="form-control" rows="3" required 
                                                      placeholder="Jelaskan alasan penolakan..."></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada peminjaman</p>
                            <a href="{{ route('borrowings.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Buat Peminjaman Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-0 py-3">
        {{ $borrowings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection