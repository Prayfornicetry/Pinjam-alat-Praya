@extends('layouts.app')

@section('title', 'Edit Alat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-pencil me-2 text-warning"></i>Edit Alat
        </h4>
        <p class="text-muted mb-0">Update informasi alat</p>
    </div>
    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">Informasi Alat</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code" class="form-label">Kode Alat <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $item->code) }}" 
                                       placeholder="Contoh: ELEC-001" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
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
                                        <option value="{{ $cat->id }}" 
                                                {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Alat <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $item->name) }}" 
                               placeholder="Contoh: Laptop Dell Inspiron 15" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Deskripsi alat...">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_total" class="form-label">Total Stok <span class="text-danger">*</span></label>
                                <input type="number" name="stock_total" id="stock_total" 
                                       class="form-control @error('stock_total') is-invalid @enderror" 
                                       value="{{ old('stock_total', $item->stock_total) }}" 
                                       min="0" required>
                                @error('stock_total')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="stock_available" class="form-label">Stok Tersedia <span class="text-danger">*</span></label>
                                <input type="number" name="stock_available" id="stock_available" 
                                       class="form-control @error('stock_available') is-invalid @enderror" 
                                       value="{{ old('stock_available', $item->stock_available) }}" 
                                       min="0" required>
                                @error('stock_available')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Tidak boleh lebih dari total stok</small>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="condition" class="form-label">Kondisi <span class="text-danger">*</span></label>
                                <select name="condition" id="condition" 
                                        class="form-select @error('condition') is-invalid @enderror" required>
                                    <option value="baik" {{ old('condition', $item->condition) == 'baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="rusak_ringan" {{ old('condition', $item->condition) == 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="rusak_berat" {{ old('condition', $item->condition) == 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                @error('condition')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Alat</label>
                        
                        @if($item->image && file_exists(public_path('storage/' . $item->image)))
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $item->image) }}" 
                                 alt="{{ $item->name }}" 
                                 class="img-thumbnail" 
                                 style="max-height: 150px;">
                            <p class="text-muted small mt-1">Gambar saat ini</p>
                        </div>
                        @endif
                        
                        <input type="file" name="image" id="image" 
                               class="form-control @error('image') is-invalid @enderror" 
                               accept="image/*" 
                               onchange="previewImage(this)">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Upload baru untuk mengganti gambar</small>
                        
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Preview" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" 
                                   class="form-check-input" value="1" 
                                   {{ old('is_active', $item->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan alat ini</label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Update
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📊 Info Alat</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">ID</td>
                        <td class="fw-bold">#{{ $item->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kode</td>
                        <td class="fw-bold">{{ $item->code }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diupdate</td>
                        <td>{{ $item->updated_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Dipinjam</td>
                        <td>
                            <span class="badge bg-info">{{ $item->borrowings()->count() }}x</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Stok tersedia tidak boleh lebih dari total stok
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Upload gambar untuk identifikasi lebih mudah
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Update kondisi alat secara berkala
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        Nonaktifkan alat yang tidak digunakan
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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