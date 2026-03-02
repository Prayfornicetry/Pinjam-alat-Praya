@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Kategori Baru
        </h4>
        <p class="text-muted mb-0">Lengkapi form di bawah ini</p>
    </div>
    <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">Informasi Kategori</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="Contoh: Elektronik" 
                               required
                               oninput="generateSlug()">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">URL</span>
                            <input type="text" name="slug" id="slug" 
                                   class="form-control @error('slug') is-invalid @enderror" 
                                   value="{{ old('slug') }}" 
                                   placeholder="elektronik">
                        </div>
                        <small class="text-muted">Kosongkan untuk generate otomatis dari nama</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3" 
                                  placeholder="Deskripsi kategori...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" 
                                   class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan kategori ini</label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">💡 Tips</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Nama unik:</strong> Tidak boleh sama dengan kategori lain
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Slug:</strong> URL friendly, gunakan huruf kecil & strip
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Deskripsi:</strong> Opsional, untuk informasi tambahan
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Status:</strong> Nonaktifkan jika tidak digunakan
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📊 Statistik</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <i class="bi bi-folder fs-1 text-primary"></i>
                    <p class="mb-0 mt-2 text-muted">Kategori baru akan muncul di daftar</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateSlug() {
    const name = document.getElementById('name').value;
    const slugInput = document.getElementById('slug');
    
    // Hanya auto-generate jika slug kosong atau sama dengan slug sebelumnya
    if (!slugInput.value || slugInput.dataset.generated === 'true') {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        
        slugInput.value = slug;
        slugInput.dataset.generated = 'true';
    }
}

// Stop auto-generate jika user edit manual
document.getElementById('slug').addEventListener('input', function() {
    this.dataset.generated = 'false';
});
</script>
@endpush