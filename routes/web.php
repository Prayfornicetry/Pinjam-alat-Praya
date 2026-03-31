<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DiscountController; 

// ==========================================
//    ROOT ROUTE (Langsung ke Login/Dashboard)
// ==========================================
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// ==========================================
// GUEST ROUTES (Belum Login)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// ==========================================
// AUTHENTICATED ROUTES (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {
    
    // ------------------------------------------
    // SEMUA USER BISA AKSES (Admin, Staff, User)
    // ------------------------------------------
    
    // Logout & Profile
    Route::post('/logout', [ProfileController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Dashboard (redirect based on role in controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->name('staff.dashboard');
    
    // Notification Routes (All Users)
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    
    // User Borrowing Routes (All Users)
    Route::get('/borrowing-request', [BorrowingController::class, 'createRequest'])->name('borrowing.request.create');
    Route::post('/borrowing-request', [BorrowingController::class, 'storeRequest'])->name('borrowing.request.store');
    Route::get('/my-borrowings', [BorrowingController::class, 'myBorrowings'])->name('borrowings.my');
    Route::get('/my-borrowings/{id}', [BorrowingController::class, 'showMyBorrowing'])->name('borrowings.my.show');
    
    // History (All Users - see their own based on role)
    Route::get('/borrowings-history', [BorrowingController::class, 'history'])->name('borrowings.history');
    
    // ------------------------------------------
    // USER READ-ONLY ITEMS
    // ------------------------------------------
    Route::middleware('role:user')->group(function () {
        Route::get('/catalog/items', [ItemController::class, 'userIndex'])->name('items.user.index');
        Route::get('/catalog/items/{id}', [ItemController::class, 'userShow'])->name('items.user.show');
    });
    
    // ------------------------------------------
    // ADMIN & STAFF ONLY 🔒 (Full CRUD)
    // ------------------------------------------
    Route::middleware('role:admin,staff')->group(function () {
        // Items CRUD (Admin & Staff)
        Route::resource('items', ItemController::class);
        
        // Categories CRUD
        Route::resource('categories', CategoryController::class);
        
        // Borrowings CRUD
        Route::resource('borrowings', BorrowingController::class);
        Route::post('borrowings/{id}/approve', [BorrowingController::class, 'approve'])->name('borrowings.approve');
        Route::post('borrowings/{id}/reject', [BorrowingController::class, 'reject'])->name('borrowings.reject');
        Route::post('borrowings/{id}/return', [BorrowingController::class, 'returnItem'])->name('borrowings.return');
    });
    
    // ------------------------------------------
    // ADMIN ONLY 🔒
    // ------------------------------------------
    Route::middleware('role:admin')->group(function () {
        // Users CRUD
        Route::resource('users', UserController::class);
        
        // Discount Management (Admin Only)
        Route::resource('discounts', DiscountController::class);
        Route::post('/discounts/validate-code', [DiscountController::class, 'validateCode'])->name('discounts.validate-code');
        
        // Payment verification
        Route::post('/borrowings/{id}/verify-payment', [BorrowingController::class, 'verifyPayment'])->name('borrowings.verify-payment');
        
        // Settings
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');
        Route::post('settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
        Route::post('settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
        Route::post('settings/optimize', [SettingsController::class, 'optimize'])->name('settings.optimize');
        
        // Reports
        Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
        Route::get('reports/pdf', [ReportsController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('reports/excel', [ReportsController::class, 'exportExcel'])->name('reports.excel');
        Route::get('reports/inventory', [ReportsController::class, 'inventoryReport'])->name('reports.inventory');
    });
});