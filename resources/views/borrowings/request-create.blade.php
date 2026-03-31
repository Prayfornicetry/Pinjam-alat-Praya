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
    <!-- Form Section -->
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
                                        data-discount-percent="{{ $item->discount_percentage ?? 0 }}"
                                        data-late-fee="{{ $item->late_fee ?? 0 }}">
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
                    <div class="row g-3">
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
                                <small class="text-danger" id="dateWarning" style="display:none;">
                                    <i class="bi bi-exclamation-circle"></i> Tanggal kembali harus setelah tanggal pinjam!
                                </small>
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
                    
                    <!-- Payment Method -->
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
                    
                    <!-- Discount Code -->
                    <div class="mb-3">
                        <label for="discount_code" class="form-label">Kode Diskon (Opsional)</label>
                        <div class="input-group">
                            <input type="text" name="discount_code" id="discount_code" 
                                   class="form-control" 
                                   value="{{ old('discount_code') }}" 
                                   placeholder="Masukkan kode kupon (contoh: DISKON10)">
                            <button type="button" class="btn btn-outline-primary" onclick="validateDiscount()">
                                <i class="bi bi-check-circle"></i> Terapkan
                            </button>
                        </div>
                        <small id="discountMessage" class="text-muted"></small>
                    </div>
                    
                    <!-- Price Breakdown (REAL-TIME) -->
                    <div id="priceBreakdown" class="mt-4 p-4 bg-light rounded border" style="display:none;">
                        <h6 class="mb-3 fw-bold text-primary">📊 Rincian Perhitungan</h6>
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
                                <td class="text-muted">Subtotal Sewa:</td>
                                <td class="text-end fw-bold" id="subtotal">Rp 0</td>
                            </tr>
                            <tr id="discountRow" style="display:none;">
                                <td class="text-success">Diskon (<span id="discountLabel"></span>):</td>
                                <td class="text-end fw-bold text-success" id="discountAmount">- Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Setelah Diskon:</td>
                                <td class="text-end fw-bold" id="totalAfterDiscount">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="text-info">Deposit/Jaminan:</td>
                                <td class="text-end fw-bold text-info" id="deposit">Rp 0</td>
                            </tr>
                            <tr class="border-top pt-2">
                                <td class="text-muted fw-bold fs-5">Total Bayar:</td>
                                <td class="text-end fw-bold text-primary fs-4" id="grandTotal">Rp 0</td>
                            </tr>
                        </table>
                        
                        <div class="alert alert-info mt-3 mb-0 py-2 small">
                            <i class="bi bi-info-circle me-1"></i>
                            <strong>Catatan:</strong> Jika terjadi keterlambatan, akan dikenakan denda sebesar <strong id="lateFeeDisplay">Rp 0</strong>/hari.
                        </div>
                    </div>
                    
                    <!-- Info Alert -->
                    <div class="alert alert-warning mt-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Penting:</strong> Peminjaman akan berstatus <strong>Pending</strong> dan menunggu approval dari admin/staff. Pastikan tanggal sesuai kebutuhan.
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="bi bi-save me-2"></i>Ajukan Peminjaman
                        </button>
                        <a href="{{ route('borrowings.my') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar: Alur & Tips -->
    <div class="col-lg-4">
        <!-- Alur Peminjaman -->
        <div class="card border-0 shadow-sm mb-4">
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
                <div class="d-flex">
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Pilih alat dengan stok mencukupi.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Pastikan tanggal kembali setelah tanggal pinjam.
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Gunakan kode diskon jika Anda memilikinya.
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Periksa rincian harga sebelum mengajukan.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentDiscountData = null;

// ✅ Calculate Price Function
function calculatePrice() {
    const itemSelect = document.getElementById('item_id');
    const borrowDateInput = document.getElementById('borrow_date');
    const returnDateInput = document.getElementById('return_date');
    const dateWarning = document.getElementById('dateWarning');
    const submitBtn = document.getElementById('submitBtn');
    
    const borrowDate = new Date(borrowDateInput.value);
    const returnDate = new Date(returnDateInput.value);
    
    // Reset warning
    dateWarning.style.display = 'none';
    returnDateInput.classList.remove('is-invalid');

    if (!itemSelect.value || !borrowDateInput.value || !returnDateInput.value) {
        document.getElementById('priceBreakdown').style.display = 'none';
        submitBtn.disabled = true;
        return;
    }

    // Validate Dates
    if (returnDate <= borrowDate) {
        dateWarning.style.display = 'block';
        returnDateInput.classList.add('is-invalid');
        document.getElementById('priceBreakdown').style.display = 'none';
        submitBtn.disabled = true;
        return;
    }

    const selectedOption = itemSelect.options[itemSelect.selectedIndex];
    const isMember = true; // User yang login adalah member
    
    // Get Values from Data Attributes
    let pricePerDay = parseFloat(selectedOption.dataset.memberPrice) > 0 
        ? parseFloat(selectedOption.dataset.memberPrice) 
        : parseFloat(selectedOption.dataset.price || 0);
    
    const deposit = parseFloat(selectedOption.dataset.deposit || 0);
    const lateFee = parseFloat(selectedOption.dataset.lateFee || 0);
    const hasItemDiscount = selectedOption.dataset.hasDiscount === '1';
    const discountPercent = parseFloat(selectedOption.dataset.discountPercent || 0);
    
    // Calculate Total Days
    const oneDay = 24 * 60 * 60 * 1000;
    const totalDays = Math.round(Math.abs((returnDate - borrowDate) / oneDay)) + 1;
    
    // Apply Item Discount if active
    let finalPricePerDay = pricePerDay;
    let itemDiscountAmount = 0;
    
    if (hasItemDiscount && discountPercent > 0) {
        itemDiscountAmount = pricePerDay * (discountPercent / 100);
        finalPricePerDay = pricePerDay - itemDiscountAmount;
    }
    
    // Calculate Subtotal
    const subtotal = finalPricePerDay * totalDays;
    
    // Apply Coupon Discount (if validated)
    let couponDiscountAmount = 0;
    if (currentDiscountData && currentDiscountData.valid) {
        if (currentDiscountData.type === 'percentage') {
            couponDiscountAmount = subtotal * (currentDiscountData.value / 100);
            // Max discount cap if exists
            if (currentDiscountData.max_discount && couponDiscountAmount > currentDiscountData.max_discount) {
                couponDiscountAmount = currentDiscountData.max_discount;
            }
        } else {
            couponDiscountAmount = currentDiscountData.value;
        }
    }
    
    const totalAfterDiscount = subtotal - couponDiscountAmount;
    const grandTotal = totalAfterDiscount + deposit;
    
    // Update UI
    document.getElementById('pricePerDay').textContent = formatRupiah(finalPricePerDay);
    document.getElementById('totalDays').textContent = totalDays + ' hari';
    document.getElementById('subtotal').textContent = formatRupiah(subtotal);
    
    // Handle Coupon Discount Row
    const discountRow = document.getElementById('discountRow');
    if (couponDiscountAmount > 0) {
        discountRow.style.display = 'table-row';
        document.getElementById('discountLabel').textContent = currentDiscountData.name;
        document.getElementById('discountAmount').textContent = '- ' + formatRupiah(couponDiscountAmount);
    } else {
        discountRow.style.display = 'none';
    }
    
    document.getElementById('totalAfterDiscount').textContent = formatRupiah(totalAfterDiscount);
    document.getElementById('deposit').textContent = formatRupiah(deposit);
    document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
    document.getElementById('lateFeeDisplay').textContent = formatRupiah(lateFee);
    
    document.getElementById('priceBreakdown').style.display = 'block';
    submitBtn.disabled = false;
}

// ✅ Validate Discount Code via AJAX
function validateDiscount() {
    const code = document.getElementById('discount_code').value.trim();
    const message = document.getElementById('discountMessage');
    const submitBtn = document.getElementById('submitBtn');
    
    if (!code) {
        message.textContent = '❌ Masukkan kode diskon terlebih dahulu.';
        message.className = 'text-danger small';
        currentDiscountData = null;
        calculatePrice(); // Recalculate without discount
        return;
    }
    
    message.textContent = '⏳ Memvalidasi...';
    message.className = 'text-muted small';
    
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
            currentDiscountData = {
                valid: true,
                name: data.discount.name,
                type: data.discount.type,
                value: parseFloat(data.discount.value),
                max_discount: data.discount.max_discount ? parseFloat(data.discount.max_discount) : null
            };
            
            message.textContent = `✅ Diskon valid: ${data.discount.name} (${data.discount.value}${data.discount.type === 'percentage' ? '%' : ''})`;
            message.className = 'text-success small fw-bold';
            calculatePrice(); // Recalculate with new discount
        } else {
            currentDiscountData = null;
            message.textContent = `❌ ${data.message}`;
            message.className = 'text-danger small';
            calculatePrice(); // Recalculate without discount
        }
    })
    .catch(error => {
        currentDiscountData = null;
        message.textContent = '❌ Terjadi kesalahan koneksi. Coba lagi.';
        message.className = 'text-danger small';
        calculatePrice();
    });
}

// Helper: Format Rupiah
function formatRupiah(number) {
    return 'Rp ' + number.toLocaleString('id-ID', {minimumFractionDigits: 0, maximumFractionDigits: 0});
}

// Auto-calculate on page load if values exist
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});
</script>
@endpush