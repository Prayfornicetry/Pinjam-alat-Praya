@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-person me-2 text-primary"></i>Profil Saya
        </h4>
        <p class="text-muted mb-0">Informasi akun Anda</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('profile.edit') }}" class="btn btn-warning">
            <i class="bi bi-pencil me-2"></i>Edit Profil
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body text-center">
                @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center mx-auto mb-3" 
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                
                @php
                    $roleBadge = [
                        'admin' => 'bg-danger',
                        'staff' => 'bg-warning',
                        'user' => 'bg-info'
                    ][$user->role] ?? 'bg-secondary';
                @endphp
                <span class="badge {{ $roleBadge }} px-3 py-2">
                    {{ ucfirst($user->role) }}
                </span>
                
                <div class="mt-3">
                    @if($user->is_active)
                        <span class="badge bg-success">
                            <i class="bi bi-check-circle me-1"></i>Aktif
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="bi bi-x-circle me-1"></i>Nonaktif
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Informasi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted">ID User</td>
                        <td class="fw-bold">#{{ $user->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Telepon</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Bergabung</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Total Pinjam</h6>
                        <h3 class="mb-0">{{ $stats['total_borrowings'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-warning text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Sedang Pinjam</h6>
                        <h3 class="mb-0">{{ $stats['active_borrowings'] }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white text-center">
                    <div class="card-body py-3">
                        <h6 class="mb-0 opacity-75">Sudah Kembali</h6>
                        <h3 class="mb-0">{{ $stats['returned'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Peminjaman (5 Terakhir)
                </h6>
            </div>
            <div class="card-body p-0">
                @php
                    $recentBorrowings = $user->borrowings()
                        ->with('item')
                        ->latest()
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentBorrowings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Alat</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBorrowings as $borrowing)
                            <tr>
                                <td class="ps-4">
                                    <h6 class="mb-0">{{ $borrowing->item->name ?? '-' }}</h6>
                                    <small class="text-muted">{{ $borrowing->item->code ?? '-' }}</small>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($borrowing->borrow_date)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($borrowing->return_date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $borrowing->status === 'approved' ? 'success' : ($borrowing->status === 'pending' ? 'warning' : ($borrowing->status === 'returned' ? 'info' : 'danger')) }}">
                                        {{ ucfirst($borrowing->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">Belum ada riwayat peminjaman</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection