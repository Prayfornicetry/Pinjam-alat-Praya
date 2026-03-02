<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Models\Item;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        // Calculate storage used
        $storageUsed = $this->getStorageUsed();
        
        return view('settings.index', compact('storageUsed'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');
        $validated = [];
        
        // Validation per tab
        if ($tab === 'general') {
            $validated = $request->validate([
                'app_name' => 'required|string|max:100',
                'app_short_name' => 'nullable|string|max:20',
                'app_description' => 'nullable|string',
                'app_footer' => 'nullable|string',
                'app_version' => 'nullable|string|max:20',
                'app_timezone' => 'nullable|string|max:50',
                'app_logo' => 'nullable|image|max:2048',
                'app_favicon' => 'nullable|image|max:500',
            ]);
        } elseif ($tab === 'email') {
            $validated = $request->validate([
                'mail_host' => 'nullable|string',
                'mail_port' => 'nullable|numeric',
                'mail_username' => 'nullable|email',
                'mail_password' => 'nullable|string',
                'mail_encryption' => 'nullable|in:tls,ssl',
                'mail_from_address' => 'nullable|email',
                'mail_from_name' => 'nullable|string',
            ]);
        } elseif ($tab === 'loan') {
            $validated = $request->validate([
                'loan_max_days' => 'required|numeric|min:1|max:365',
                'loan_max_items' => 'required|numeric|min:1|max:100',
                'loan_overdue_fine' => 'nullable|numeric|min:0',
                'loan_grace_period' => 'nullable|numeric|min:0|max:7',
                'loan_auto_approve' => 'nullable|boolean',
                'loan_allow_overdue' => 'nullable|boolean',
                'loan_require_approval' => 'nullable|boolean',
            ]);
        } elseif ($tab === 'notification') {
            $validated = $request->validate([
                'notification_email_enabled' => 'nullable|boolean',
                'notification_reminder_days' => 'nullable|numeric|min:0|max:30',
                'notification_reminder_morning' => 'nullable|boolean',
                'notification_reminder_evening' => 'nullable|boolean',
                'notification_approval' => 'nullable|boolean',
                'notification_rejection' => 'nullable|boolean',
                'notification_returned' => 'nullable|boolean',
                'notification_overdue' => 'nullable|boolean',
            ]);
        }

        // Handle file uploads
        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo');
            if ($oldLogo) {
                Storage::disk('public')->delete($oldLogo);
            }
            $validated['app_logo'] = $request->file('app_logo')->store('settings', 'public');
        }

        if ($request->hasFile('app_favicon')) {
            $oldFavicon = Setting::get('app_favicon');
            if ($oldFavicon) {
                Storage::disk('public')->delete($oldFavicon);
            }
            $validated['app_favicon'] = $request->file('app_favicon')->store('settings', 'public');
        }

        // Convert boolean fields
        $booleanFields = [
            'loan_auto_approve',
            'loan_allow_overdue',
            'loan_require_approval',
            'notification_email_enabled',
            'notification_reminder_morning',
            'notification_reminder_evening',
            'notification_approval',
            'notification_rejection',
            'notification_returned',
            'notification_overdue',
        ];

        foreach ($booleanFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = $request->has($field) ? '1' : '0';
            }
        }

        // Save settings
        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        $tabNames = [
            'general' => 'Umum',
            'email' => 'Email',
            'loan' => 'Peminjaman',
            'notification' => 'Notifikasi',
        ];

        return back()->with('success', '✅ Pengaturan ' . ($tabNames[$tab] ?? '') . ' berhasil disimpan!');
    }

    /**
     * Test email configuration
     */
    public function testEmail()
    {
        try {
            $email = auth()->user()->email;
            
            Mail::raw('Test email dari E-Lending System', function($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - E-Lending System');
            });

            return response()->json([
                'success' => true,
                'message' => 'Email test berhasil dikirim ke ' . $email
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Backup database
     */
    public function backup()
    {
        try {
            $filename = 'backup_' . date('Y-m-d_His') . '.sql';
            $path = storage_path('app/backups/' . $filename);
            
            if (!Storage::disk('local')->exists('backups')) {
                Storage::disk('local')->makeDirectory('backups');
            }
            
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                config('database.connections.mysql.host'),
                config('database.connections.mysql.database'),
                $path
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0) {
                return back()->with('success', '✅ Backup berhasil! File: ' . $filename);
            } else {
                return back()->with('error', '❌ Gagal backup database');
            }
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return back()->with('success', '✅ Cache berhasil dibersihkan!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Optimize application
     */
    public function optimize()
    {
        try {
            Artisan::call('optimize');
            
            return back()->with('success', '✅ Aplikasi berhasil dioptimasi!');
        } catch (\Exception $e) {
            return back()->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Get storage used
     */
    private function getStorageUsed()
    {
        $totalSize = 0;
        $files = Storage::allFiles();
        
        foreach ($files as $file) {
            $totalSize += Storage::size($file);
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $totalSize > 0 ? floor(log($totalSize, 1024)) : 0;
        
        return number_format($totalSize / pow(1024, $power), 2) . ' ' . $units[$power];
    }
}