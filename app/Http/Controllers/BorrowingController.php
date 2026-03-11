<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Item;
use App\Models\User;
use App\Models\Discount;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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
        
        // Get active discounts
        $activeDiscounts = Discount::where('is_active', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->get();
        
        return view('borrowings.create', compact('items', 'users', 'activeDiscounts'));
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
            // ✅ TAMBAHAN: Payment & Discount fields
            'payment_method' => 'required|in:transfer,qris,cash',
            'discount_code' => 'nullable|string|max:50',
        ], [
            'user_id.required' => 'Peminjam harus dipilih',
            'item_id.required' => 'Alat harus dipilih',
            'borrow_date.required' => 'Tanggal pinjam harus diisi',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu',
            'return_date.required' => 'Tanggal kembali harus diisi',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal pinjam',
            'payment_method.required' => 'Metode pembayaran harus dipilih',
        ]);

        // Cek stok tersedia
        $item = Item::findOrFail($validated['item_id']);
        if ($item->stock_available <= 0) {
            return back()->with('error', '❌ Stok alat tidak tersedia!')->withInput();
        }
        
        // ✅ HITUNG HARGA
        $user = User::findOrFail($validated['user_id']);
        $priceData = $this->calculatePrice(
            $item, 
            $validated['borrow_date'], 
            $validated['return_date'], 
            $validated['discount_code'] ?? null,
            $user->role === 'user'
        );

        // Buat peminjaman
        $borrowing = Borrowing::create([
            'user_id' => $validated['user_id'],
            'item_id' => $validated['item_id'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            // ✅ PRICE FIELDS
            'rental_price_per_day' => $priceData['price_per_day'],
            'total_days' => $priceData['total_days'],
            'subtotal' => $priceData['subtotal'],
            'discount_code' => $validated['discount_code'] ?? null,
            'discount_amount' => $priceData['discount_amount'],
            'total_after_discount' => $priceData['total_after_discount'],
            'deposit_paid' => $priceData['deposit'],
            'grand_total' => $priceData['grand_total'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
        ]);

        return redirect()->route('borrowings.index')
            ->with('success', '✅ Peminjaman berhasil dibuat! Total: Rp ' . number_format($priceData['grand_total'], 0, ',', '.'));
    }

    /**
     * Display the specified borrowing
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'item.category', 'approvedBy', 'payments'])
            ->findOrFail($id);
        
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
            'approved_by' => Auth::id(),
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
            'approved_by' => Auth::id(),
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
        
        // ✅ HITUNG DENDA KETERLAMBATAN
        $lateFee = $this->calculateLateFee(
            $borrowing->item, 
            $borrowing->return_date, 
            Carbon::today()
        );
        
        // Update status
        $borrowing->update([
            'status' => 'returned',
            'actual_return_date' => Carbon::today(),
            'late_fee' => $lateFee,
            'grand_total' => $borrowing->grand_total + $lateFee,
            'payment_status' => $lateFee > 0 ? 'pending' : 'paid',
        ]);

        $message = '✅ Alat berhasil dikembalikan!';
        if ($lateFee > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($lateFee, 0, ',', '.');
        }

        return back()->with('success', $message);
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
     * ✅ Show detail of user's own borrowing
     */
    public function showMyBorrowing($id)
    {
        $borrowing = Borrowing::where('user_id', Auth::id())
            ->with(['item.category', 'approvedBy'])
            ->findOrFail($id);
        
        return view('borrowings.my-show', compact('borrowing'));
    }

    /**
     * ✅ Form create peminjaman untuk User
     */
    public function createRequest()
    {
        $items = Item::where('stock_available', '>', 0)
            ->where('is_active', true)
            ->where('condition', '!=', 'rusak_berat')
            ->with('category')
            ->get();
        
        // Get active discounts
        $activeDiscounts = Discount::where('is_active', true)
            ->where('start_date', '<=', Carbon::today())
            ->where('end_date', '>=', Carbon::today())
            ->get();
        
        return view('borrowings.request-create', compact('items', 'activeDiscounts'));
    }

    /**
     * ✅ Store peminjaman dari User - UPDATED
     */
    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'borrow_date' => 'required|date|after_or_equal:today',
            'return_date' => 'required|date|after:borrow_date',
            'notes' => 'nullable|string|max:500',
            // ✅ TAMBAHAN: Payment & Discount fields
            'payment_method' => 'required|in:transfer,qris,cash',
            'discount_code' => 'nullable|string|max:50',
        ], [
            'item_id.required' => 'Alat harus dipilih',
            'borrow_date.required' => 'Tanggal pinjam harus diisi',
            'borrow_date.after_or_equal' => 'Tanggal pinjam tidak boleh di masa lalu',
            'return_date.required' => 'Tanggal kembali harus diisi',
            'return_date.after' => 'Tanggal kembali harus setelah tanggal pinjam',
            'payment_method.required' => 'Metode pembayaran harus dipilih',
        ]);

        $item = Item::findOrFail($validated['item_id']);
        
        if ($item->stock_available <= 0) {
            return back()->with('error', '❌ Stok alat tidak tersedia!')->withInput();
        }
        
        // ✅ HITUNG HARGA
        $priceData = $this->calculatePrice(
            $item, 
            $validated['borrow_date'], 
            $validated['return_date'], 
            $validated['discount_code'] ?? null,
            true // User is member
        );

        Borrowing::create([
            'user_id' => Auth::id(),
            'item_id' => $validated['item_id'],
            'borrow_date' => $validated['borrow_date'],
            'return_date' => $validated['return_date'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            // ✅ PRICE FIELDS
            'rental_price_per_day' => $priceData['price_per_day'],
            'total_days' => $priceData['total_days'],
            'subtotal' => $priceData['subtotal'],
            'discount_code' => $validated['discount_code'] ?? null,
            'discount_amount' => $priceData['discount_amount'],
            'total_after_discount' => $priceData['total_after_discount'],
            'deposit_paid' => $priceData['deposit'],
            'grand_total' => $priceData['grand_total'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
        ]);

        return redirect()->route('borrowings.my')
            ->with('success', '✅ Permintaan peminjaman berhasil dibuat! Total: Rp ' . number_format($priceData['grand_total'], 0, ',', '.'));
    }

    /**
     * ✅ Validate discount code (AJAX)
     */
    public function validateDiscountCode(Request $request)
    {
        $discount = Discount::where('code', $request->code)->first();
        
        if (!$discount) {
            return response()->json(['valid' => false, 'message' => 'Kode diskon tidak valid']);
        }

        if (!$discount->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Diskon tidak aktif atau sudah kadaluarsa']);
        }

        return response()->json([
            'valid' => true,
            'discount' => [
                'name' => $discount->name,
                'type' => $discount->type,
                'value' => $discount->value,
            ]
        ]);
    }

    // ==========================================
    // ✅ HELPER METHODS (YANG KURANG!)
    // ==========================================

    /**
     * ✅ Calculate borrowing price
     */
    private function calculatePrice($item, $borrowDate, $returnDate, $discountCode = null, $isMember = false)
    {
        $borrowDate = Carbon::parse($borrowDate);
        $returnDate = Carbon::parse($returnDate);
        $totalDays = $borrowDate->diffInDays($returnDate) + 1;
        
        // Harga per hari
        $pricePerDay = $isMember && $item->member_price > 0 ? $item->member_price : $item->rental_price;
        
        // Cek diskon item
        if ($item->hasActiveDiscount()) {
            $discount = $pricePerDay * ($item->discount_percentage / 100);
            $pricePerDay -= $discount;
        }
        
        // Subtotal
        $subtotal = $pricePerDay * $totalDays;
        
        // Diskon kode kupon
        $discountAmount = 0;
        if ($discountCode) {
            $discount = Discount::where('code', $discountCode)->first();
            if ($discount && $discount->isValid()) {
                $discountAmount = $discount->calculateDiscount($subtotal);
                $discount->incrementUsage();
            }
        }
        
        $totalAfterDiscount = $subtotal - $discountAmount;
        $grandTotal = $totalAfterDiscount + $item->deposit;
        
        return [
            'price_per_day' => $pricePerDay,
            'total_days' => $totalDays,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'total_after_discount' => $totalAfterDiscount,
            'deposit' => $item->deposit,
            'grand_total' => $grandTotal,
        ];
    }

    /**
     * ✅ Calculate late fee
     */
    private function calculateLateFee($item, $returnDate, $actualReturnDate = null)
    {
        $returnDate = Carbon::parse($returnDate);
        $actualReturn = $actualReturnDate ? Carbon::parse($actualReturnDate) : Carbon::today();
        
        if ($actualReturn->lte($returnDate)) {
            return 0;
        }
        
        $lateDays = $returnDate->diffInDays($actualReturn);
        return $item->late_fee * $lateDays;
    }
}