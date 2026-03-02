<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;      // ✅ WAJIB ADA!
use Illuminate\Support\Facades\Storage;

class BorrowingController extends Controller
{
    /**
     * Display a listing of borrowings
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'item', 'approvedBy']);
        
        // User biasa hanya bisa lihat peminjaman mereka sendiri
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('item', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        return view('borrowings.index', compact('borrowings'));
    }

    /**
     * Show the form for creating a new borrowing (Admin/Staff)
     */
    public function create()
    {
        $items = Item::where('stock_available', '>', 0)
            ->where('is_active', true)
            ->where('condition', '!=', 'rusak_berat')
            ->with('category')
            ->get();
        
        $users = User::where('is_active', true)->get();
        
        return view('borrowings.create', compact('items', 'users'));
    }

    /**
     * Store a newly created borrowing (Admin/Staff)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'borrow_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string|max:500',
        ], [
            'user_id.required' => 'Peminjam harus dipilih',
            'item_id.required' => 'Alat harus dipilih',
            'borrow_date.required' => 'Tanggal pinjam harus diisi',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu',
            'return_date.required' => 'Tanggal kembali harus diisi',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal pinjam',
        ]);

        // Cek stok tersedia
        $item = Item::findOrFail($validated['item_id']);
        if ($item->stock_available <= 0) {
            return back()->with('error', '❌ Stok alat tidak tersedia!')->withInput();
        }

        // Buat peminjaman
        $borrowing = Borrowing::create([
            'user_id' => $validated['user_id'],
            'item_id' => $validated['item_id'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', '✅ Peminjaman berhasil dibuat! Menunggu approval.');
    }

    /**
     * Display the specified borrowing
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'item.category', 'approvedBy'])->findOrFail($id);
        return view('borrowings.show', compact('borrowing'));
    }

    /**
     * Show the form for editing the specified borrowing
     */
    public function edit($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        // Hanya bisa edit jika status pending
        if ($borrowing->status !== 'pending') {
            return back()->with('error', '❌ Hanya peminjaman pending yang bisa diedit!');
        }
        
        $items = Item::where('stock_available', '>', 0)
            ->where('is_active', true)
            ->get();
        
        $users = User::where('is_active', true)->get();
        
        return view('borrowings.edit', compact('borrowing', 'items', 'users'));
    }

    /**
     * Update the specified borrowing
     */
    public function update(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        if ($borrowing->status !== 'pending') {
            return back()->with('error', '❌ Hanya peminjaman pending yang bisa diedit!');
        }
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'item_id' => 'required|exists:items,id',
            'borrow_date' => 'required|date',
            'return_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string|max:500',
        ]);

        $borrowing->update($validated);

        return redirect()->route('borrowings.index')
            ->with('success', '✅ Peminjaman berhasil diupdate!');
    }

    /**
     * Remove the specified borrowing
     */
    public function destroy($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        // Hanya bisa hapus jika pending atau rejected
        if (!in_array($borrowing->status, ['pending', 'rejected'])) {
            return back()->with('error', '❌ Hanya peminjaman pending/rejected yang bisa dihapus!');
        }
        
        $borrowing->delete();

        return redirect()->route('borrowings.index')
            ->with('success', '✅ Peminjaman berhasil dihapus!');
    }

    /**
     * Approve a borrowing
     */
    public function approve($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        if ($borrowing->status !== 'pending') {
            return back()->with('error', '❌ Hanya peminjaman pending yang bisa disetujui!');
        }
        
        // Cek stok lagi
        $item = $borrowing->item;
        if ($item->stock_available <= 0) {
            return back()->with('error', '❌ Stok alat tidak tersedia!');
        }
        
        // Kurangi stok
        $item->decrement('stock_available');
        
        // Update status
        $borrowing->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),  // ✅ Pakai Auth::id()
        ]);

        return back()->with('success', '✅ Peminjaman berhasil disetujui!');
    }

    /**
     * Reject a borrowing
     */
    public function reject(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        if ($borrowing->status !== 'pending') {
            return back()->with('error', '❌ Hanya peminjaman pending yang bisa ditolak!');
        }
        
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $borrowing->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'approved_by' => Auth::id(),  // ✅ Pakai Auth::id()
        ]);

        return back()->with('success', '❌ Peminjaman ditolak!');
    }

    /**
     * Return a borrowed item
     */
    public function returnItem($id)
    {
        $borrowing = Borrowing::findOrFail($id);
        
        if ($borrowing->status !== 'approved') {
            return back()->with('error', '❌ Hanya peminjaman approved yang bisa dikembalikan!');
        }
        
        // Kembalikan stok
        $borrowing->item->increment('stock_available');
        
        // Update status
        $borrowing->update([
            'status' => 'returned',
            'actual_return_date' => Carbon::today(),
        ]);

        return back()->with('success', '✅ Alat berhasil dikembalikan!');
    }

    /**
     * History - Admin/Staff lihat SEMUA, User lihat MILIK SENDIRI
     */
    public function history(Request $request)
    {
        $query = Borrowing::with(['user', 'item.category', 'approvedBy'])
            ->where('status', 'returned');
        
        // User biasa hanya bisa lihat riwayat mereka sendiri
        if (Auth::user()->isUser()) {
            $query->where('user_id', Auth::id());
        }
        
        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })->orWhereHas('item', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter Date Range
        if ($request->filled('start_date')) {
            $query->whereDate('actual_return_date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('actual_return_date', '<=', $request->end_date);
        }
        
        $borrowings = $query->latest('actual_return_date')->paginate(15);
        
        // Stats untuk history
        $totalReturned = Borrowing::where('status', 'returned')->count();
        $totalOnTime = Borrowing::where('status', 'returned')
            ->whereColumn('actual_return_date', '<=', 'return_date')
            ->count();
        $totalLate = Borrowing::where('status', 'returned')
            ->whereColumn('actual_return_date', '>', 'return_date')
            ->count();
        
        return view('borrowings.history', compact('borrowings', 'totalReturned', 'totalOnTime', 'totalLate'));
    }

    /**
     * User's own borrowings
     */
    public function myBorrowings(Request $request)
    {
        $query = Borrowing::where('user_id', Auth::id())
            ->with(['item', 'approvedBy']);
        
        // Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $borrowings = $query->latest()->paginate(10);
        
        return view('borrowings.my', compact('borrowings'));
    }

    /**
     * ✅ Show detail of user's own borrowing (METHOD YANG KURANG!)
     */
    public function showMyBorrowing($id)
    {
        $borrowing = Borrowing::where('user_id', Auth::id())
            ->with(['item.category', 'approvedBy'])
            ->findOrFail($id);
        
        return view('borrowings.my-show', compact('borrowing'));
    }

    /**
     * ✅ Form create peminjaman untuk User (METHOD YANG KURANG!)
     */
    public function createRequest()
    {
        $items = Item::where('stock_available', '>', 0)
            ->where('is_active', true)
            ->where('condition', '!=', 'rusak_berat')
            ->with('category')
            ->get();
        
        return view('borrowings.request-create', compact('items'));
    }

    /**
     * ✅ Store peminjaman dari User (METHOD YANG KURANG!)
     */
    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrow_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string|max:500',
        ], [
            'item_id.required' => 'Alat harus dipilih',
            'borrow_date.required' => 'Tanggal pinjam harus diisi',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu',
            'return_date.required' => 'Tanggal kembali harus diisi',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal pinjam',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        if ($item->stock_available <= 0) {
            return back()->with('error', '❌ Stok alat tidak tersedia!')->withInput();
        }

        Borrowing::create([
            'user_id' => Auth::id(),
            'item_id' => $validated['item_id'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('borrowings.my')
            ->with('success', '✅ Permintaan peminjaman berhasil dibuat! Menunggu approval.');
    }

    
}