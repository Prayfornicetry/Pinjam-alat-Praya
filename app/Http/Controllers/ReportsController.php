<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BorrowingsReportExport;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Default date range (bulan ini)
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        // Report type
        $reportType = $request->type ?? 'borrowings';
        
        // Query berdasarkan type
        $query = Borrowing::with(['user', 'item.category', 'approvedBy']);
        
        // Filter Date Range
        $query->whereBetween('borrow_date', [$startDate, $endDate]);
        
        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter Kategori
        if ($request->filled('category_id')) {
            $query->whereHas('item', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }
        
        // Filter User
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $borrowings = $query->latest()->get();
        
        // Stats
        $totalBorrowings = $borrowings->count();
        $totalApproved = $borrowings->where('status', 'approved')->count();
        $totalReturned = $borrowings->where('status', 'returned')->count();
        $totalRejected = $borrowings->where('status', 'rejected')->count();
        $totalPending = $borrowings->where('status', 'pending')->count();
        
        // Chart Data (per hari)
        $chartData = $this->getChartData($startDate, $endDate);
        
        // Top Borrowers
        $topBorrowers = $this->getTopBorrowers($startDate, $endDate);
        
        // Top Items
        $topItems = $this->getTopItems($startDate, $endDate);
        
        return view('reports.index', compact(
            'borrowings', 'totalBorrowings', 'totalApproved', 'totalReturned', 
            'totalRejected', 'totalPending', 'chartData', 'topBorrowers', 'topItems',
            'startDate', 'endDate', 'reportType'
        ));
    }
    
    /**
     * Get Chart Data per hari
     */
    private function getChartData($startDate, $endDate)
    {
        $data = [];
        $labels = [];
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($start <= $end) {
            $date = $start->format('Y-m-d');
            $count = Borrowing::whereDate('borrow_date', $date)->count();
            
            $labels[] = $start->format('d M');
            $data[] = $count;
            
            $start->addDay();
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Get Top Borrowers
     */
    private function getTopBorrowers($startDate, $endDate)
    {
        return User::withCount(['borrowings' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('borrow_date', [$startDate, $endDate]);
        }])
        ->orderBy('borrowings_count', 'desc')
        ->take(5)
        ->get();
    }
    
    /**
     * Get Top Items
     */
    private function getTopItems($startDate, $endDate)
    {
        return Item::with(['category'])
            ->withCount(['borrowings' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('borrow_date', [$startDate, $endDate]);
            }])
            ->orderBy('borrowings_count', 'desc')
            ->take(5)
            ->get();
    }
    
    /**
     * Export PDF
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        $query = Borrowing::with(['user', 'item.category', 'approvedBy'])
            ->whereBetween('borrow_date', [$startDate, $endDate]);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $borrowings = $query->latest()->get();
        
        $pdf = Pdf::loadView('reports.pdf', compact('borrowings', 'startDate', 'endDate'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('laporan-peminjaman-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Export Excel
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->format('Y-m-d');
        
        return Excel::download(new BorrowingsReportExport($startDate, $endDate), 
            'laporan-peminjaman-' . date('Y-m-d') . '.xlsx');
    }
    
    /**
     * Laporan Stok Alat
     */
    public function inventoryReport(Request $request)
    {
        $query = Item::with(['category', 'borrowings']);
        
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        $items = $query->latest()->get();
        
        $totalItems = $items->count();
        $totalStock = $items->sum('stock_total');
        $availableStock = $items->sum('stock_available');
        $lowStock = $items->where('stock_available', '<=', 2)->count();
        
        return view('reports.inventory', compact('items', 'totalItems', 'totalStock', 'availableStock', 'lowStock'));
    }
}