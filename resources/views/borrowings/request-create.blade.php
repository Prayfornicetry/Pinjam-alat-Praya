@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
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
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📝 Form Peminjaman</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('borrowing.request.store') }}" method="POST">
                    @csrf
                    
                    <!-- Item Selection -->
                    <div class="mb-3">
                        <label for="item_id" class="form-label">Alat yang Dipinjam <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" required onchange="calculatePrice()">
                            <option value="">-- Pilih Alat --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" 
                                        {{ old('item_id') == $item->id ? 'selected' : '' }}
                                        data-price="{{ $item->rental_price ?? 0 }}"
                                        data-member-price="{{ $item->member_price ?? 0 }}"
                                        data-deposit="{{ $item->deposit ?? 0 }}"
                                        data-has-discount="{{ $item->has_discount ?? 0 }}"
                                        data-discount-percent="{{ $item->discount_percentage ?? 0 }}">
                                    {{ $item->name }} - {{ $item->code }} (Stok: {{ $item->stock_available }})
                                    @if($item->rental_price > 0)
                                        - Rp {{ number_format($item->rental_price, 0, ',', '.') }}/hari
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Hanya alat dengan stok tersedia yang ditampilkan</small>
                    </div>
                    
                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="borrow_date" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
                                <input type="date" name="borrow_date" id="borrow_date" 
                                      class="form-control @error('borrow_date') is-invalid @enderror" 
                                      value="{{ old('borrow_date', date('Y-m-d')) }}" 
                                      min="{{ date('Y-m-d') }}" required onchange="calculatePrice()">
                                @error('borrow_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="return_date" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                                <input type="date" name="return_date" id="return_date" 
                                      class="form-control @error('return_date') is-invalid @enderror" 
                                      value="{{ old('return_date') }}" 
                                      min="{{ date('Y-m-d', strtotime('+1 day')) }}" required onchange="calculatePrice()">
                                @error('return_date')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan / Keperluan</label>
                        <textarea name="notes" id="notes" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Contoh: Untuk acara kampus...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- ✅ TAMBAHAN: Payment Method -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">-- Pilih Metode --</option>
                            <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="qris" {{ old('payment_method') == 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Tunai</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- ✅ TAMBAHAN: Discount Code -->
                    <div class="mb-3">
                        <label for="discount_code" class="form-label">Kode Diskon (Opsional)</label>
                        <div class="input-group">
                            <input type="text" name="discount_code" id="discount_code" 
                                   class="form-control" 
                                   value="{{ old('discount_code') }}" 
                                   placeholder="Masukkan kode diskon">
                            <button type="button" class="btn btn-outline-primary" onclick="validateDiscount()">
                                <i class="bi bi-check-circle"></i> Terapkan
                            </button>
                        </div>
                        <small id="discountMessage" class="text-muted"></small>
                    </div>
                    
                    <!-- ✅ TAMBAHAN: Price Breakdown -->
                    <div id="priceBreakdown" class="mt-3 p-3 bg-light rounded" style="display:none;">
                        <h6 class="mb-3">📊 Rincian Harga</h6>
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Harga per Hari:</td>
                                <td class="text-end fw-bold" id="pricePerDay">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Durasi:</td>
                                <td class="text-end fw-bold" id="totalDays">0 hari</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Subtotal:</td>
                                <td class="text-end fw-bold" id="subtotal">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diskon:</td>
                                <td class="text-end text-success fw-bold" id="discountAmount">- Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Deposit:</td>
                                <td class="text-end fw-bold" id="deposit">Rp 0</td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted fw-bold">Total Bayar:</td>
                                <td class="text-end text-primary fw-bold" id="grandTotal">Rp 0</td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Info Alert -->
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Peminjaman akan berstatus <strong>Pending</strong> dan menunggu approval dari admin/staff.
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Buttons -->
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
    
    <!-- Sidebar: Alur Peminjaman -->
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
        
        <!-- Tips Card -->
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Pilih alat dengan stok tersedia
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Pastikan tanggal kembali setelah tanggal pinjam
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Gunakan kode diskon jika ada
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Periksa kembali sebelum mengajukan
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ✅ Calculate Price
function calculatePrice() {
    const itemSelect = document.getElementById('item_id');
    const borrowDate = new Date(document.getElementById('borrow_date').value);
    const returnDate = new Date(document.getElementById('return_date').value);
    
    if (!itemSelect.value || !borrowDate || !returnDate || isNaN(borrowDate) || isNaN(returnDate)) {
        document.getElementById('priceBreakdown').style.display = 'none';
        return;
    }
    
    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const isMember = true; // User yang login adalah member
    const pricePerDay = isMember && selectedOption.dataset.memberPrice > 0 
        ? parseFloat(selectedOption.dataset.memberPrice) 
        : parseFloat(selectedOption.dataset.price || 0);
    const deposit = parseFloat(selectedOption.dataset.deposit || 0);
    const hasDiscount = selectedOption.dataset.hasDiscount === '1';
    const discountPercent = parseFloat(selectedOption.dataset.discountPercent || 0);
    
    // Calculate total days
    const totalDays = Math.ceil((returnDate - borrowDate) / (1000 * 60 * 60 * 24)) + 1;
    
    // Calculate subtotal
    let finalPricePerDay = pricePerDay;
    if (hasDiscount && discountPercent > 0) {
        finalPricePerDay = pricePerDay - (pricePerDay * discountPercent / 100);
    }
    
    const subtotal = finalPricePerDay * totalDays;
    const grandTotal = subtotal + deposit;
    
    // Update display
    document.getElementById('pricePerDay').textContent = 'Rp ' + finalPricePerDay.toLocaleString('id-ID', {minimumFractionDigits: 0});
    document.getElementById('totalDays').textContent = totalDays + ' hari';
    document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID', {minimumFractionDigits: 0});
    document.getElementById('deposit').textContent = 'Rp ' + deposit.toLocaleString('id-ID', {minimumFractionDigits: 0});
    document.getElementById('grandTotal').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID', {minimumFractionDigits: 0});
    
    document.getElementById('priceBreakdown').style.display = 'block';
}

// ✅ Validate Discount Code
function validateDiscount() {
    const code = document.getElementById('discount_code').value;
    const message = document.getElementById('discountMessage');
    
    if (!code) {
        message.textContent = '❌ Masukkan kode diskon';
        message.className = 'text-danger';
        return;
    }
    
    message.textContent = '⏳ Memvalidasi...';
    message.className = 'text-muted';
    
    fetch('{{ route("discounts.validate-code") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            message.textContent = '✅ Diskon valid: ' + data.discount.name + ' (' + data.discount.value + (data.discount.type === 'percentage' ? '%' : '') + ')';
            message.className = 'text-success';
        } else {
            message.textContent = '❌ ' + data.message;
            message.className = 'text-danger';
        }
    })
    .catch(error => {
        message.textContent = '❌ Terjadi kesalahan. Coba lagi.';
        message.className = 'text-danger';
    });
}

// Auto-calculate on page load if values exist
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>
@endpush