@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-pencil me-2 text-warning"></i>Edit User
        </h4>
        <p class="text-muted mb-0">Update informasi user</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Form Edit User -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📝 Informasi User</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <!-- Name & Email -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                </div>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input type="email" name="email" id="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                </div>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           placeholder="Kosongkan jika tidak diubah">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimal 8 karakter</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-lock-fill"></i>
                                    </span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control" 
                                           placeholder="Ulangi password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Role & Phone -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role / Jabatan <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person-badge"></i>
                                    </span>
                                    <select name="role" id="role" 
                                            class="form-select @error('role') is-invalid @enderror" 
                                            required>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                                            👤 User (Peminjam)
                                        </option>
                                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>
                                            👨‍💼 Staff (Petugas)
                                        </option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                            👑 Admin (Administrator)
                                        </option>
                                    </select>
                                </div>
                                @error('role')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Nomor Telepon / WhatsApp</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-phone"></i>
                                    </span>
                                    <input type="text" name="phone" id="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone', $user->phone) }}" 
                                           placeholder="08123456789">
                                </div>
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Avatar -->
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Foto Profil</label>
                        
                        @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                        <div class="mb-3 text-center">
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="{{ $user->name }}" 
                                 class="rounded-circle shadow-sm" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            <p class="text-muted small mt-2 mb-0">Foto saat ini</p>
                        </div>
                        @else
                        <div class="mb-3 text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                                 style="width: 100px; height: 100px;">
                                <i class="bi bi-person-circle text-muted" style="font-size: 3rem;"></i>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Belum ada foto</p>
                        </div>
                        @endif
                        
                        <input type="file" name="avatar" id="avatar" 
                               class="form-control form-control-sm @error('avatar') is-invalid @enderror" 
                               accept="image/*" 
                               onchange="previewImage(this)">
                        @error('avatar')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Upload baru untuk mengganti foto (JPG, PNG, Max 2MB)</small>
                        
                        <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                            <img src="" alt="Preview" class="rounded-circle shadow-sm" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                            <p class="text-muted small mt-2 mb-0">Preview foto baru</p>
                        </div>
                    </div>
                    
                    <!-- Status Active -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="is_active" id="is_active" 
                                   class="form-check-input" value="1" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Aktifkan user ini</strong>
                                <small class="text-muted d-block">User nonaktif tidak bisa login</small>
                            </label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil me-2"></i>Update User
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Info User Card - COMPACT VERSION -->
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold">👤 Info User</h6>
            </div>
            <div class="card-body py-3">
                @if($user->avatar && file_exists(public_path('storage/' . $user->avatar)))
                <div class="text-center mb-3">
                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                         alt="{{ $user->name }}" 
                         class="rounded-circle shadow-sm" 
                         style="width: 80px; height: 80px; object-fit: cover;">
                </div>
                @endif
                
                <table class="table table-sm table-borderless mb-0" style="font-size: 0.8rem;">
                    <tr>
                        <td class="text-muted py-1">ID</td>
                        <td class="fw-bold text-end py-1">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Nama</td>
                        <td class="fw-bold text-end py-1" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Email</td>
                        <td class="text-end py-1" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; font-size: 0.75rem;">{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Role</td>
                        <td class="text-end py-1">
                            @php
                                $badgeClass = [
                                    'admin' => 'bg-danger',
                                    'staff' => 'bg-warning',
                                    'user' => 'bg-info'
                                ][$user->role] ?? 'bg-secondary';
                                
                                $roleIcon = [
                                    'admin' => '👑',
                                    'staff' => '👨‍💼',
                                    'user' => '👤'
                                ][$user->role] ?? '👤';
                            @endphp
                            <span class="badge {{ $badgeClass }} py-1" style="font-size: 0.7rem;">{{ $roleIcon }} {{ ucfirst($user->role) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Status</td>
                        <td class="text-end py-1">
                            @if($user->is_active)
                                <span class="badge bg-success py-1" style="font-size: 0.7rem;">✅ Aktif</span>
                            @else
                                <span class="badge bg-danger py-1" style="font-size: 0.7rem;">❌ Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Bergabung</td>
                        <td class="text-end py-1" style="font-size: 0.75rem;">{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Update</td>
                        <td class="text-end py-1" style="font-size: 0.75rem;">{{ $user->updated_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted py-1">Total Pinjam</td>
                        <td class="text-end py-1">
                            <span class="badge bg-primary py-1" style="font-size: 0.7rem;">{{ $user->borrowings()->count() }}x</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Quick Stats - COMPACT -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-2">
                <h6 class="mb-0 fw-bold">📊 Statistik</h6>
            </div>
            <div class="card-body py-3">
                @php
                    $activeBorrowings = $user->borrowings()->where('status', 'approved')->count();
                    $pendingBorrowings = $user->borrowings()->where('status', 'pending')->count();
                    $returnedBorrowings = $user->borrowings()->where('status', 'returned')->count();
                @endphp
                
                <div class="row g-2">
                    <div class="col-4">
                        <div class="text-center p-2 bg-light rounded">
                            <h6 class="mb-0 text-primary fw-bold" style="font-size: 1rem;">{{ $activeBorrowings }}</h6>
                            <small class="text-muted" style="font-size: 0.6rem;">Aktif</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 bg-light rounded">
                            <h6 class="mb-0 text-warning fw-bold" style="font-size: 1rem;">{{ $pendingBorrowings }}</h6>
                            <small class="text-muted" style="font-size: 0.6rem;">Pending</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-center p-2 bg-light rounded">
                            <h6 class="mb-0 text-success fw-bold" style="font-size: 1rem;">{{ $returnedBorrowings }}</h6>
                            <small class="text-muted" style="font-size: 0.6rem;">Selesai</small>
                        </div>
                    </div>
                </div>
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

// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
        input.type = 'text';
    } else {
        input.type = 'password';
    }
}
</script>
@endpush