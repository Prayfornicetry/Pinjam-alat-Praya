@extends('layouts.auth')

@section('title', 'Login')

@section('branding')
<div class="text-center">
    <i class="bi bi-box-seam-fill brand-logo"></i>
    <h2 class="mb-3">E-Lending System</h2>
    <p class="mb-4">Sistem Peminjaman Alat Terpadu</p>
    <div class="mt-5">
        <div class="d-flex justify-content-center gap-3">
            <div class="text-center">
                <i class="bi bi-shield-check fs-1"></i>
                <p class="small mb-0">Aman</p>
            </div>
            <div class="text-center">
                <i class="bi bi-speedometer2 fs-1"></i>
                <p class="small mb-0">Cepat</p>
            </div>
            <div class="text-center">
                <i class="bi bi-cloud-check fs-1"></i>
                <p class="small mb-0">Online</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="mb-4">
    <h3 class="fw-bold mb-2">Selamat Datang! 👋</h3>
    <p class="text-muted">Silakan login untuk melanjutkan</p>
</div>

<form action="{{ route('login') }}" method="POST">
    @csrf
    
    <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
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
    
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
            <span class="input-group-text bg-light">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   class="form-control @error('password') is-invalid @enderror" 
                   id="password" 
                   name="password" 
                   placeholder="••••••••"
                   required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
            </button>
        </div>
        @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember">
                Ingat Saya
            </label>
        </div>
        <a href="#" class="text-decoration-none small">Lupa Password?</a>
    </div>
    
    <button type="submit" class="btn btn-primary w-100 mb-3">
        <i class="bi bi-box-arrow-in-right me-2"></i> Login
    </button>
</form>

<div class="text-center">
    <p class="mb-2 text-muted">Belum punya akun?</p>
    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
        <i class="bi bi-person-plus me-2"></i> Daftar Sekarang
    </a>
</div>

<div class="mt-4 text-center">
    <small class="text-muted">
        Demo Login:<br>
        <strong>Admin:</strong> admin@elending.com / password
    </small>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>
@endpush