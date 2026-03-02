@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-pencil me-2 text-warning"></i>Edit Kategori
        </h4>
        <p class="text-muted mb-0">Update informasi kategori</p>
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
                <form action="{{ route('categories.update', $category->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $category->name) }}" 
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
                                   value="{{ old('slug', $category->slug) }}">
                        </div>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea name="description" id="description" 
                                  class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" 
                                   class="form-check-input" value="1" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktifkan kategori ini</label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil me-2"></i>Update
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
                <h6 class="mb-0 fw-bold">📊 Info Kategori</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted">ID</td>
                        <td class="fw-bold">#{{ $category->id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td>{{ $category->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diupdate</td>
                        <td>{{ $category->updated_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jumlah Alat</td>
                        <td>
                            <span class="badge bg-info">{{ $category->items->count() }} Alat</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($category->items->count() > 0)
        <div class="alert alert-warning mt-3 mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Kategori ini memiliki {{ $category->items->count() }} alat. Tidak dapat dihapus.
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateSlug() {
    const name = document.getElementById('name').value;
    const slugInput = document.getElementById('slug');
    
    if (!slugInput.value || slugInput.dataset.generated === 'true') {
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/(^-|-$)/g, '');
        
        slugInput.value = slug;
        slugInput.dataset.generated = 'true';
    }
}

document.getElementById('slug').addEventListener('input', function() {
    this.dataset.generated = 'false';
});
</script>
@endpush