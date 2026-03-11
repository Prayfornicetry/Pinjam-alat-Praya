@extends('layouts.app')

@section('title', 'Tambah Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Alat Baru
        </h4>
        <p class="text-muted mb-0">Lengkapi form di bawah ini</p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📝 Informasi Alat</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Basic Info -->
                    <h6 class="mb-3 text-primary">📋 Informasi Dasar</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Alat <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" 
                                      class="form-control @error('code') is-invalid @enderror" 
                                      value="{{ old('code') }}" 
                                      placeholder="Contoh: ELEC-001" required>
                                @error('code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="category_id" 
                                       class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach(\App\Models\Category::all() as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" 
                                      class="form-control @error('name') is-invalid @enderror" 
                                      value="{{ old('name') }}" 
                                      placeholder="Contoh: Laptop Dell Inspiron 15" required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea name="description" id="description" 
                                         class="form-control @error('description') is-invalid @enderror" 
                                         rows="3" 
                                         placeholder="Deskripsi alat...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stock & Condition -->
                    <h6 class="mb-3 text-primary mt-4">📦 Stok & Kondisi</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_total" class="form-label">Total Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stock_total" id="stock_total" 
                                      class="form-control @error('stock_total') is-invalid @enderror" 
                                      value="{{ old('stock_total', 0) }}" 
                                      min="0" required>
                                @error('stock_total')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_available" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
                                <input type="number" name="stock_available" id="stock_available" 
                                      class="form-control @error('stock_available') is-invalid @enderror" 
                                      value="{{ old('stock_available', 0) }}" 
                                      min="0" required>
                                @error('stock_available')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tidak boleh lebih dari total stok</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="condition" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select name="condition" id="condition" 
                                       class="form-select @error('condition') is-invalid @enderror" required>
                                    <option value="baik" {{ old('condition') == 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak_ringan" {{ old('condition') == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="rusak_berat" {{ old('condition') == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- ✅ TAMBAHAN: Price & Rental Fields -->
                    <h6 class="mb-3 text-primary mt-4">💰 Harga & Sewa</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rental_price" class="form-label">Harga Sewa Normal (per hari) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="rental_price" id="rental_price" 
                                          class="form-control @error('rental_price') is-invalid @enderror" 
                                          value="{{ old('rental_price', 0) }}" 
                                          min="0" step="1000" required>
                                </div>
                                @error('rental_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="member_price" class="form-label">Harga Member (per hari)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="member_price" id="member_price" 
                                          class="form-control @error('member_price') is-invalid @enderror" 
                                          value="{{ old('member_price', 0) }}" 
                                          min="0" step="1000">
                                </div>
                                @error('member_price')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Kosongkan jika sama dengan harga normal</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="late_fee" class="form-label">Denda Keterlambatan (per hari)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="late_fee" id="late_fee" 
                                          class="form-control @error('late_fee') is-invalid @enderror" 
                                          value="{{ old('late_fee', 0) }}" 
                                          min="0" step="1000">
                                </div>
                                @error('late_fee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="deposit" class="form-label">Deposit/Jaminan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="deposit" id="deposit" 
                                          class="form-control @error('deposit') is-invalid @enderror" 
                                          value="{{ old('deposit', 0) }}" 
                                          min="0" step="1000">
                                </div>
                                @error('deposit')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- ✅ TAMBAHAN: Discount Fields -->
                    <h6 class="mb-3 text-primary mt-4">🏷️ Diskon</h6>
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input type="checkbox" name="has_discount" id="has_discount" 
                                      class="form-check-input" value="1" 
                                      {{ old('has_discount') ? 'checked' : '' }} 
                                      onchange="toggleDiscountFields()">
                                <label class="form-check-label" for="has_discount">
                                    <strong>Aktifkan Diskon</strong>
                                </label>
                            </div>
                            
                            <div id="discountFields" style="{{ old('has_discount') ? '' : 'display:none;' }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="discount_percentage" class="form-label">Persentase Diskon (%)</label>
                                            <input type="number" name="discount_percentage" id="discount_percentage" 
                                                  class="form-control" 
                                                  value="{{ old('discount_percentage', 0) }}" 
                                                  min="0" max="100">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="discount_start" class="form-label">Tanggal Mulai</label>
                                            <input type="date" name="discount_start" id="discount_start" 
                                                  class="form-control" 
                                                  value="{{ old('discount_start') }}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="discount_end" class="form-label">Tanggal Akhir</label>
                                            <input type="date" name="discount_end" id="discount_end" 
                                                  class="form-control" 
                                                  value="{{ old('discount_end') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Image -->
                    <h6 class="mb-3 text-primary mt-4">🖼️ Gambar Alat</h6>
                    <div class="mb-3">
                        <label for="image" class="form-label">Upload Gambar</label>
                        <input type="file" name="image" id="image" 
                              class="form-control @error('image') is-invalid @enderror" 
                              accept="image/*" 
                              onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG, Max 2MB</small>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" 
                                  class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan alat ini</label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Sidebar: Tips -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Gunakan kode unik untuk setiap alat
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Upload gambar untuk identifikasi lebih mudah
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Stok tersedia tidak boleh lebih dari total stok
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Set harga sewa untuk sistem rental
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Update kondisi alat secara berkala
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview Image
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Toggle Discount Fields
function toggleDiscountFields() {
    const checkbox = document.getElementById('has_discount');
    const fields = document.getElementById('discountFields');
    fields.style.display = checkbox.checked ? '' : 'none';
}

// Auto-validate stock
document.getElementById('stock_available').addEventListener('input', function() {
    const totalStock = parseInt(document.getElementById('stock_total').value) || 0;
    const availableStock = parseInt(this.value) || 0;
    
    if (availableStock > totalStock) {
        this.setCustomValidity('Stok tersedia tidak boleh lebih dari total stok!');
        this.classList.add('is-invalid');
    } else {
        this.setCustomValidity('');
        this.classList.remove('is-invalid');
    }
});

document.getElementById('stock_total').addEventListener('input', function() {
    document.getElementById('stock_available').dispatchEvent(new Event('input'));
});
</script>
@endpush