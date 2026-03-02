@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-calendar-check me-2 text-primary"></i>Peminjaman Saya
        </h4>
        <p class="text-muted mb-0">Riwayat peminjaman Anda</p>
    </div>
    <a href="{{ route('borrowing.request.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Ajukan Peminjaman
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4">
    @php
        $total = \App\Models\Borrowing::where('user_id', Auth::id())->count();
        $pending = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'pending')->count();
        $approved = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'approved')->count();
        $returned = \App\Models\Borrowing::where('user_id', Auth::id())->where('status', 'returned')->count();
    @endphp
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75">Total</h6>
                <h3 class="mb-0">{{ $total }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75">Pending</h6>
                <h3 class="mb-0">{{ $pending }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75">Disetujui</h6>
                <h3 class="mb-0">{{ $approved }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body text-center py-3">
                <h6 class="mb-0 opacity-75">Selesai</h6>
                <h3 class="mb-0">{{ $returned }}</h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">Daftar Peminjaman ({{ $borrowings->total() }})</h6>
            <form action="{{ route('borrowings.my') }}" method="GET" class="d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($borrowings as $borrowing)
                    <tr>
                        <td class="ps-4">
                            <div>
                                <h6 class="mb-0">{{ $borrowing->item->name ?? '-' }}</h6>
                                <small class="text-muted">{{ $borrowing->item->code ?? '-' }}</small>
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
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($borrowing->status) }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('borrowings.my.show', $borrowing->id) }}" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
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
    <div class="card-footer bg-white border-0 py-3">
        {{ $borrowings->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection