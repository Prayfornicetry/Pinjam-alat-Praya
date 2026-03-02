@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Ajukan Peminjaman Alat
        </h4>
        <p class="text-muted mb-0">Isi form di bawah untuk mengajukan peminjaman</p>
    </div>
    <a href="{{ route('borrowings.my') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">Form Peminjaman</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('borrowing.request.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Alat yang Dipinjam <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">Pilih Alat</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} - {{ $item->code }} (Stok: {{ $item->stock_available }})
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Hanya alat dengan stok tersedia yang ditampilkan</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="borrow_date" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="borrow_date" id="borrow_date" 
                                       class="form-control @error('borrow_date') is-invalid @enderror" 
                                       value="{{ old('borrow_date', date('Y-m-d')) }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('borrow_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="return_date" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="return_date" id="return_date" 
                                       class="form-control @error('return_date') is-invalid @enderror" 
                                       value="{{ old('return_date') }}" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('return_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan / Keperluan</label>
                        <textarea name="notes" id="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Contoh: Untuk acara kampus...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Peminjaman akan berstatus <strong>Pending</strong> dan menunggu approval dari admin/staff.
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Ajukan Peminjaman
                        </button>
                        <a href="{{ route('borrowings.my') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Alur Peminjaman</h6>
            </div>
            <div class="card-body">
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center" 
                             style="width: 30px; height: 30px;">1</div>
                    </div>
                    <div>
                        <h6 class="mb-0">Ajukan Peminjaman</h6>
                        <small class="text-muted">Status: Pending</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <div class="bg-warning text-white rounded-circle d-flex justify-content-center align-items-center" 
                             style="width: 30px; height: 30px;">2</div>
                    </div>
                    <div>
                        <h6 class="mb-0">Approval Admin/Staff</h6>
                        <small class="text-muted">Status: Approved/Rejected</small>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3">
                        <div class="bg-success text-white rounded-circle d-flex justify-content-center align-items-center" 
                             style="width: 30px; height: 30px;">3</div>
                    </div>
                    <div>
                        <h6 class="mb-0">Pengembalian</h6>
                        <small class="text-muted">Status: Returned</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection