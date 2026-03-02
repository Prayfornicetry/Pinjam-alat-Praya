@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-0 fw-bold text-dark">
            <i class="bi bi-gear me-2 text-primary"></i>Pengaturan Sistem
        </h4>
        <p class="text-muted mb-0">Konfigurasi dan pengaturan aplikasi secara menyeluruh</p>
    </div>
    <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-2"></i>Refresh
    </button>
</div>

<!-- Flash Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-circle-fill me-2"></i> 
    <strong>Terjadi kesalahan:</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button">
            <i class="bi bi-sliders me-2"></i>Umum
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button">
            <i class="bi bi-envelope me-2"></i>Email
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="loan-tab" data-bs-toggle="tab" data-bs-target="#loan" type="button">
            <i class="bi bi-calendar-check me-2"></i>Peminjaman
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="notification-tab" data-bs-toggle="tab" data-bs-target="#notification" type="button">
            <i class="bi bi-bell me-2"></i>Notifikasi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button">
            <i class="bi bi-hdd me-2"></i>Sistem
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="settingsTabsContent">
    
    <!-- ==========================================
         GENERAL SETTINGS
         ========================================== -->
    <div class="tab-pane fade show active" id="general" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">🏷️ Pengaturan Umum Aplikasi</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="tab" value="general">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                                <input type="text" name="app_name" id="app_name" 
                                       class="form-control @error('app_name') is-invalid @enderror" 
                                       value="{{ old('app_name', \App\Models\Setting::get('app_name', 'E-Lending System')) }}" 
                                       required>
                                @error('app_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_short_name" class="form-label">Nama Singkat</label>
                                <input type="text" name="app_short_name" id="app_short_name" 
                                       class="form-control @error('app_short_name') is-invalid @enderror" 
                                       value="{{ old('app_short_name', \App\Models\Setting::get('app_short_name', 'ELS')) }}">
                                @error('app_short_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Untuk tampilan mobile</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_logo" class="form-label">Logo Aplikasi</label>
                                <input type="file" name="app_logo" id="app_logo" 
                                       class="form-control @error('app_logo') is-invalid @enderror" 
                                       accept="image/*" 
                                       onchange="previewImage(this, 'logoPreview')">
                                @error('app_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG, Max 2MB</small>
                                
                                @if(\App\Models\Setting::get('app_logo'))
                                <div class="mt-2" id="logoPreview">
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('app_logo')) }}" 
                                         alt="Logo" 
                                         class="img-thumbnail" 
                                         style="max-height: 80px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage('app_logo')">
                                        <i class="bi bi-trash"></i> Hapus Logo
                                    </button>
                                </div>
                                @else
                                <div class="mt-2" id="logoPreview"></div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_favicon" class="form-label">Favicon</label>
                                <input type="file" name="app_favicon" id="app_favicon" 
                                       class="form-control @error('app_favicon') is-invalid @enderror" 
                                       accept="image/x-icon,image/png" 
                                       onchange="previewImage(this, 'faviconPreview')">
                                @error('app_favicon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: ICO, PNG, Max 500KB</small>
                                
                                @if(\App\Models\Setting::get('app_favicon'))
                                <div class="mt-2" id="faviconPreview">
                                    <img src="{{ asset('storage/' . \App\Models\Setting::get('app_favicon')) }}" 
                                         alt="Favicon" 
                                         class="img-thumbnail" 
                                         style="max-height: 40px;">
                                </div>
                                @else
                                <div class="mt-2" id="faviconPreview"></div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="app_description" class="form-label">Deskripsi Aplikasi</label>
                        <textarea name="app_description" id="app_description" 
                                  class="form-control @error('app_description') is-invalid @enderror" 
                                  rows="3">{{ old('app_description', \App\Models\Setting::get('app_description', 'Sistem Peminjaman Alat Terpadu')) }}</textarea>
                        @error('app_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_version" class="form-label">Versi Aplikasi</label>
                                <input type="text" name="app_version" id="app_version" 
                                       class="form-control @error('app_version') is-invalid @enderror" 
                                       value="{{ old('app_version', \App\Models\Setting::get('app_version', '1.0.0')) }}">
                                @error('app_version')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_footer" class="form-label">Text Footer</label>
                                <input type="text" name="app_footer" id="app_footer" 
                                       class="form-control @error('app_footer') is-invalid @enderror" 
                                       value="{{ old('app_footer', \App\Models\Setting::get('app_footer', '© ' . date('Y') . ' E-Lending System. All rights reserved.')) }}">
                                @error('app_footer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="app_timezone" class="form-label">Zona Waktu</label>
                        <select name="app_timezone" id="app_timezone" 
                                class="form-select @error('app_timezone') is-invalid @enderror">
                            @php
                                $timezones = [
                                    'Asia/Jakarta' => 'WIB (Jakarta)',
                                    'Asia/Makassar' => 'WITA (Makassar)',
                                    'Asia/Jayapura' => 'WIT (Jayapura)',
                                ];
                            @endphp
                            @foreach($timezones as $value => $label)
                                <option value="{{ $value }}" {{ old('app_timezone', \App\Models\Setting::get('app_timezone', 'Asia/Jakarta')) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('app_timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Pengaturan Umum
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==========================================
         EMAIL SETTINGS
         ========================================== -->
    <div class="tab-pane fade" id="email" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📧 Pengaturan Email (SMTP)</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="email">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Info:</strong> Konfigurasi SMTP untuk pengiriman email notifikasi
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">SMTP Host</label>
                                <input type="text" name="mail_host" id="mail_host" 
                                       class="form-control @error('mail_host') is-invalid @enderror" 
                                       value="{{ old('mail_host', \App\Models\Setting::get('mail_host', 'smtp.gmail.com')) }}" 
                                       placeholder="smtp.gmail.com">
                                @error('mail_host')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">SMTP Port</label>
                                <input type="number" name="mail_port" id="mail_port" 
                                       class="form-control @error('mail_port') is-invalid @enderror" 
                                       value="{{ old('mail_port', \App\Models\Setting::get('mail_port', '587')) }}" 
                                       placeholder="587">
                                @error('mail_port')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_username" class="form-label">SMTP Username (Email)</label>
                                <input type="email" name="mail_username" id="mail_username" 
                                       class="form-control @error('mail_username') is-invalid @enderror" 
                                       value="{{ old('mail_username', \App\Models\Setting::get('mail_username', '')) }}">
                                @error('mail_username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label">SMTP Password</label>
                                <div class="input-group">
                                    <input type="password" name="mail_password" id="mail_password" 
                                           class="form-control @error('mail_password') is-invalid @enderror" 
                                           value="{{ old('mail_password', \App\Models\Setting::get('mail_password', '')) }}">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('mail_password')">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                @error('mail_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Gunakan App Password jika menggunakan Gmail</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label">Encryption</label>
                                <select name="mail_encryption" id="mail_encryption" 
                                        class="form-select @error('mail_encryption') is-invalid @enderror">
                                    <option value="tls" {{ old('mail_encryption', \App\Models\Setting::get('mail_encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ old('mail_encryption', \App\Models\Setting::get('mail_encryption', 'tls')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ old('mail_encryption', \App\Models\Setting::get('mail_encryption', 'tls')) == '' ? 'selected' : '' }}>None</option>
                                </select>
                                @error('mail_encryption')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">From Email</label>
                                <input type="email" name="mail_from_address" id="mail_from_address" 
                                       class="form-control @error('mail_from_address') is-invalid @enderror" 
                                       value="{{ old('mail_from_address', \App\Models\Setting::get('mail_from_address', '')) }}">
                                @error('mail_from_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mail_from_name" class="form-label">From Name</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" 
                               class="form-control @error('mail_from_name') is-invalid @enderror" 
                               value="{{ old('mail_from_name', \App\Models\Setting::get('mail_from_name', \App\Models\Setting::get('app_name', 'E-Lending System'))) }}">
                        @error('mail_from_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Simpan
                        </button>
                        <button type="button" class="btn btn-outline-success" onclick="testEmail()">
                            <i class="bi bi-envelope-check me-2"></i>Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==========================================
         LOAN SETTINGS
         ========================================== -->
    <div class="tab-pane fade" id="loan" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">📋 Pengaturan Peminjaman</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="loan">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_max_days" class="form-label">Maksimal Hari Pinjam <span class="text-danger">*</span></label>
                                <input type="number" name="loan_max_days" id="loan_max_days" 
                                       class="form-control @error('loan_max_days') is-invalid @enderror" 
                                       value="{{ old('loan_max_days', \App\Models\Setting::get('loan_max_days', '7')) }}" 
                                       min="1" max="365" required>
                                @error('loan_max_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">1-365 hari</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_max_items" class="form-label">Maksimal Alat per User <span class="text-danger">*</span></label>
                                <input type="number" name="loan_max_items" id="loan_max_items" 
                                       class="form-control @error('loan_max_items') is-invalid @enderror" 
                                       value="{{ old('loan_max_items', \App\Models\Setting::get('loan_max_items', '3')) }}" 
                                       min="1" max="100" required>
                                @error('loan_max_items')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">1-100 alat</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_overdue_fine" class="form-label">Denda Keterlambatan (per hari)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="loan_overdue_fine" id="loan_overdue_fine" 
                                           class="form-control @error('loan_overdue_fine') is-invalid @enderror" 
                                           value="{{ old('loan_overdue_fine', \App\Models\Setting::get('loan_overdue_fine', '0')) }}" 
                                           min="0">
                                </div>
                                @error('loan_overdue_fine')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">0 = tanpa denda</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="loan_grace_period" class="form-label">Grace Period (Hari)</label>
                                <input type="number" name="loan_grace_period" id="loan_grace_period" 
                                       class="form-control @error('loan_grace_period') is-invalid @enderror" 
                                       value="{{ old('loan_grace_period', \App\Models\Setting::get('loan_grace_period', '1')) }}" 
                                       min="0" max="7">
                                @error('loan_grace_period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Toleransi keterlambatan tanpa denda</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Opsi Peminjaman</label>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="loan_auto_approve" id="loan_auto_approve" 
                                   class="form-check-input" value="1" 
                                   {{ old('loan_auto_approve', \App\Models\Setting::get('loan_auto_approve', '0')) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="loan_auto_approve">
                                <strong>Auto Approve Peminjaman</strong>
                                <small class="text-muted d-block">Peminjaman langsung disetujui tanpa approval admin</small>
                            </label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input type="checkbox" name="loan_allow_overdue" id="loan_allow_overdue" 
                                   class="form-check-input" value="1" 
                                   {{ old('loan_allow_overdue', \App\Models\Setting::get('loan_allow_overdue', '1')) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="loan_allow_overdue">
                                <strong>Izinkan Peminjaman Baru saat Ada Overdue</strong>
                                <small class="text-muted d-block">User masih bisa pinjam meski ada yang belum dikembalikan</small>
                            </label>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" name="loan_require_approval" id="loan_require_approval" 
                                   class="form-check-input" value="1" 
                                   {{ old('loan_require_approval', \App\Models\Setting::get('loan_require_approval', '1')) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="loan_require_approval">
                                <strong>Wajib Approval untuk Semua Peminjaman</strong>
                                <small class="text-muted d-block">Semua peminjaman harus disetujui admin/staff</small>
                            </label>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Pengaturan Peminjaman
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==========================================
         NOTIFICATION SETTINGS
         ========================================== -->
    <div class="tab-pane fade" id="notification" role="tabpanel">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold">🔔 Pengaturan Notifikasi</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="notification">
                    
                    <div class="mb-4">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="notification_email_enabled" id="notification_email_enabled" 
                                   class="form-check-input" value="1" 
                                   {{ old('notification_email_enabled', \App\Models\Setting::get('notification_email_enabled', '1')) == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="notification_email_enabled">
                                <strong>Aktifkan Notifikasi Email</strong>
                                <small class="text-muted d-block">Kirim email untuk approval, reminder, dan pengembalian</small>
                            </label>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">📅 Reminder Pengembalian</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notification_reminder_days" class="form-label">Reminder Sebelum Jatuh Tempo</label>
                                <input type="number" name="notification_reminder_days" id="notification_reminder_days" 
                                       class="form-control @error('notification_reminder_days') is-invalid @enderror" 
                                       value="{{ old('notification_reminder_days', \App\Models\Setting::get('notification_reminder_days', '3')) }}" 
                                       min="0" max="30">
                                @error('notification_reminder_days')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Hari sebelum tanggal kembali (0-30)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Waktu Pengiriman Reminder</label>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="notification_reminder_morning" id="notification_reminder_morning" 
                                           class="form-check-input" value="1" 
                                           {{ old('notification_reminder_morning', \App\Models\Setting::get('notification_reminder_morning', '1')) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notification_reminder_morning">Pagi (08:00)</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input type="checkbox" name="notification_reminder_evening" id="notification_reminder_evening" 
                                           class="form-check-input" value="1" 
                                           {{ old('notification_reminder_evening', \App\Models\Setting::get('notification_reminder_evening', '0')) == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notification_reminder_evening">Sore (16:00)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="mb-3 mt-4">📧 Jenis Notifikasi Email</h6>
                    
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="notification_approval" id="notification_approval" 
                               class="form-check-input" value="1" 
                               {{ old('notification_approval', \App\Models\Setting::get('notification_approval', '1')) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_approval">Notifikasi Approval Peminjaman</label>
                    </div>
                    
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="notification_rejection" id="notification_rejection" 
                               class="form-check-input" value="1" 
                               {{ old('notification_rejection', \App\Models\Setting::get('notification_rejection', '1')) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_rejection">Notifikasi Penolakan Peminjaman</label>
                    </div>
                    
                    <div class="form-check form-switch mb-2">
                        <input type="checkbox" name="notification_returned" id="notification_returned" 
                               class="form-check-input" value="1" 
                               {{ old('notification_returned', \App\Models\Setting::get('notification_returned', '1')) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_returned">Notifikasi Pengembalian Alat</label>
                    </div>
                    
                    <div class="form-check form-switch">
                        <input type="checkbox" name="notification_overdue" id="notification_overdue" 
                               class="form-check-input" value="1" 
                               {{ old('notification_overdue', \App\Models\Setting::get('notification_overdue', '1')) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="notification_overdue">Notifikasi Keterlambatan</label>
                    </div>
                    
                    <hr class="my-4">
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Simpan Pengaturan Notifikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- ==========================================
         SYSTEM SETTINGS
         ========================================== -->
    <div class="tab-pane fade" id="system" role="tabpanel">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-bold">📊 Informasi Sistem</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted">Laravel Version</td>
                                <td class="fw-bold">{{ app()->version() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">PHP Version</td>
                                <td class="fw-bold">{{ phpversion() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Database</td>
                                <td class="fw-bold">{{ config('database.default') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Timezone</td>
                                <td class="fw-bold">{{ config('app.timezone') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Debug Mode</td>
                                <td>
                                    <span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">
                                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Users</td>
                                <td class="fw-bold">{{ \App\Models\User::count() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Items</td>
                                <td class="fw-bold">{{ \App\Models\Item::count() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Borrowings</td>
                                <td class="fw-bold">{{ \App\Models\Borrowing::count() }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Storage Used</td>
                                <td class="fw-bold">{{ $storageUsed ?? '0 MB' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-bold">🔧 Maintenance</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <form action="{{ route('settings.backup') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin backup database?')">
                                    <i class="bi bi-download me-2"></i>Backup Database
                                </button>
                            </form>
                            
                            <form action="{{ route('settings.clear-cache') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin ingin clear cache?')">
                                    <i class="bi bi-trash me-2"></i>Clear Cache
                                </button>
                            </form>
                            
                            <form action="{{ route('settings.optimize') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info text-white" onclick="return confirm('Yakin ingin optimize aplikasi?')">
                                    <i class="bi bi-lightning me-2"></i>Optimize Aplikasi
                                </button>
                            </form>
                        </div>
                        
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Backup akan disimpan di <code>storage/app/backups/</code></small>
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
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            if (preview) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="img-thumbnail" style="max-height: 80px;">';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove Image
function removeImage(inputId) {
    if (confirm('Yakin ingin menghapus gambar ini?')) {
        document.getElementById(inputId).value = '';
        const preview = document.getElementById(inputId.replace('app_', '') + 'Preview');
        if (preview) {
            preview.innerHTML = '';
        }
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

// Test Email
function testEmail() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Testing...';
    btn.disabled = true;
    
    fetch('{{ route('settings.test-email') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error: ' + error);
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>
@endpush