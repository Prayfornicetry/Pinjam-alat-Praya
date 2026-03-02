<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function index()
    {
        // Redirect berdasarkan role
        if (Auth::user()->isUser()) {
            return redirect()->route('user.dashboard');
        }

        if (Auth::user()->isStaff()) {
            return redirect()->route('staff.dashboard');
        }

        // Admin Dashboard
        $totalItems = Item::count() ?? 0;
        $totalBorrowed = Borrowing::where('status', 'approved')->count() ?? 0;
        $totalReturned = Borrowing::where('status', 'returned')->count() ?? 0;
        $totalUsers = User::count() ?? 0;
        
        $today = Carbon::today();
        $overdue = Borrowing::where('status', 'approved')
            ->where('return_date', '<', $today)
            ->count() ?? 0;
        
        $pending = Borrowing::where('status', 'pending')->count() ?? 0;
        
        // Recent Activities
        $recentActivities = Borrowing::with(['user', 'item'])
            ->latest()
            ->take(5)
            ->get();
        
        // Low Stock Items
        $lowStockItems = Item::where('stock_available', '<=', 2)
            ->with('category')
            ->take(5)
            ->get();
        
        // Borrowing Stats (6 months)
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Borrowing::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $chartLabels[] = $month->format('M');
            $chartData[] = $count;
        }
        
        // Category Stats
        $categoryStats = Category::withCount('items')
            ->orderBy('items_count', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard.index', compact(
            'totalItems', 'totalBorrowed', 'totalReturned', 'totalUsers',
            'overdue', 'pending', 'recentActivities', 'lowStockItems',
            'chartLabels', 'chartData', 'categoryStats'
        ));
    }

    /**
     * Staff Dashboard
     */
    public function staffDashboard()
    {
        // Stats
        $totalBorrowings = Borrowing::count() ?? 0;
        $pending = Borrowing::where('status', 'pending')->count() ?? 0;
        $approved = Borrowing::where('status', 'approved')->count() ?? 0;
        $returned = Borrowing::where('status', 'returned')->count() ?? 0;
        $overdue = Borrowing::where('status', 'approved')
            ->where('return_date', '<', Carbon::today())
            ->count() ?? 0;
        
        // Pending Borrowings (need approval)
        $pendingBorrowings = Borrowing::with(['user', 'item'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();
        
        // Recent Activities
        $recentActivities = Borrowing::with(['user', 'item'])
            ->latest()
            ->take(5)
            ->get();
        
        return view('staff.dashboard', compact(
            'totalBorrowings', 'pending', 'approved', 'returned',
            'overdue', 'pendingBorrowings', 'recentActivities'
        ));
    }

    /**
     * User Dashboard
     */
    public function userDashboard()
    {
        $userId = Auth::id();
        
        // User Stats
        $totalBorrowings = Borrowing::where('user_id', $userId)->count() ?? 0;
        $active = Borrowing::where('user_id', $userId)
            ->where('status', 'approved')
            ->count() ?? 0;
        $pending = Borrowing::where('user_id', $userId)
            ->where('status', 'pending')
            ->count() ?? 0;
        $returned = Borrowing::where('user_id', $userId)
            ->where('status', 'returned')
            ->count() ?? 0;
        
        // ✅ My Recent Borrowings
        $myBorrowings = Borrowing::where('user_id', $userId)
            ->with('item')
            ->latest()
            ->take(5)
            ->get();
        
        // ✅ Available Items
        $availableItems = Item::where('stock_available', '>', 0)
            ->where('is_active', true)
            ->where('condition', '!=', 'rusak_berat')
            ->with('category')
            ->take(5)
            ->get();
        
        // ✅ Recent Approvals (untuk notifikasi)
        $recentApprovals = Borrowing::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with('item')
            ->latest()
            ->take(5)
            ->get();
        
        return view('user.dashboard', compact(
            'totalBorrowings', 'active', 'pending', 'returned',
            'myBorrowings', 'availableItems', 'recentApprovals'
        ));
    }
}