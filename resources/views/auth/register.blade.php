@extends('layouts.auth')

@section('title', 'Register')

@section('branding')
<div class="text-center">
    <i class="bi bi-person-plus-fill brand-logo"></i>
    <h2 class="mb-3">Buat Akun Baru</h2>
    <p class="mb-4">Bergabung dengan E-Lending System</p>
    <div class="mt-5">
        <img src="https://illustrations.popsy.co/amber/success.svg" alt="Register" class="img-fluid" style="max-height: 200px;">
    </div>
</div>
@endsection

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-2">Daftar Sekarang 🚀</h3>
    <p class="text-muted">Lengkapi form di bawah ini</p>
</div>

<!-- ✅ INFO BOX - AUTO ROLE USER -->
<div class="alert alert-info mb-4">
    <i class="bi bi-info-circle me-2"></i>
    <small>Dengan mendaftar, Anda otomatis mendapatkan <strong>role User (Peminjam)</strong>. Untuk akses Admin/Staff, hubungi administrator.</small>
</div>

<form action="{{ route('register') }}" method="POST">
    @csrf
    
    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-person"></i>
            </span>
            <input type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   placeholder="John Doe"
                   required autofocus>
        </div>
        @error('name')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <!-- Email -->
    <div class="mb-3">
        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-envelope"></i>
            </span>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   placeholder="nama@email.com"
                   required>
        </div>
        @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <!-- ✅ Phone (Optional) - SESUAI CONTROLLER -->
    <div class="mb-3">
        <label for="phone" class="form-label">Nomor Telp</label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-phone"></i>
            </span>
            <input type="text" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   id="phone" 
                   name="phone" 
                   value="{{ old('phone') }}"
                   placeholder="08xxxxxxxxxx">
        </div>
        @error('phone')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">Untuk notifikasi peminjaman</small>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="Minimal 8 karakter"
                   required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">Minimal 8 karakter, huruf dan angka</small>
    </div>
    
    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-lock-fill"></i>
            </span>
            <input type="password" 
                   class="form-control" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   placeholder="Ulangi password"
                   required>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                <i class="bi bi-eye"></i>
            </button>
        </div>
    </div>
    
    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-person-plus me-2"></i> Daftar Sekarang
    </button>
</form>

<!-- Login Link -->
<div class="text-center">
    <p class="mb-0 text-muted">Sudah punya akun? 
        <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login disini</a>
    </p>
</div>
@endsection

@push('scripts')
<script>
// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endpush