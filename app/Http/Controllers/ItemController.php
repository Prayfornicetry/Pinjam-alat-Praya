<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    /**
     * Admin/Staff - Full CRUD Index
     */
    public function index(Request $request)
    {
        $query = Item::with('category');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        $items = $query->latest()->paginate(10);
        
        return view('items.index', compact('items'));
    }

    /**
     * ✅ USER - Read-only Index (Cannot create/edit/delete)
     */
    public function userIndex(Request $request)
    {
        $query = Item::with('category')
            ->where('is_active', true);  // Hanya tampilkan alat aktif
            
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        
        $items = $query->latest()->paginate(12);
        $categories = Category::all();
        
        return view('items.user-index', compact('items', 'categories'));
    }

    /**
     * ✅ USER - Read-only Show (Cannot edit/delete)
     */
    public function userShow($id)
    {
        $item = Item::with(['category', 'borrowings' => function($q) {
            $q->where('user_id', Auth::id())->latest()->take(5);
        }])->findOrFail($id);
        
        return view('items.user-show', compact('item'));
    }

    /**
     * Admin/Staff - Create
     */
    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    /**
     * Admin/Staff - Store - ✅ UPDATED WITH PRICE FIELDS
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:items,code|max:50',
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'stock_total' => 'required|integer|min:0',
            'stock_available' => 'required|integer|min:0',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            // ✅ TAMBAHAN: Price Fields
            'rental_price' => 'required|numeric|min:0',
            'member_price' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['has_discount'] = $request->has('has_discount');
        
        // Set default values for nullable fields
        $validated['member_price'] = $validated['member_price'] ?? 0;
        $validated['late_fee'] = $validated['late_fee'] ?? 0;
        $validated['deposit'] = $validated['deposit'] ?? 0;
        $validated['discount_percentage'] = $validated['discount_percentage'] ?? 0;

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('success', '✅ Alat berhasil ditambahkan!');
    }

    /**
     * Admin/Staff - Show
     */
    public function show($id)
    {
        $item = Item::with(['category', 'borrowings'])->findOrFail($id);
        return view('items.show', compact('item'));
    }

    /**
     * Admin/Staff - Edit
     */
    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Admin/Staff - Update - ✅ UPDATED WITH PRICE FIELDS
     */
    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|unique:items,code,' . $id . '|max:50',
            'name' => 'required|string|max:150',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'stock_total' => 'required|integer|min:0',
            'stock_available' => 'required|integer|min:0',
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
            // ✅ TAMBAHAN: Price Fields
            'rental_price' => 'required|numeric|min:0',
            'member_price' => 'nullable|numeric|min:0',
            'late_fee' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'has_discount' => 'nullable|boolean',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $imagePath = $request->file('image')->store('items', 'public');
            $validated['image'] = $imagePath;
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['has_discount'] = $request->has('has_discount');
        
        // Set default values for nullable fields
        $validated['member_price'] = $validated['member_price'] ?? 0;
        $validated['late_fee'] = $validated['late_fee'] ?? 0;
        $validated['deposit'] = $validated['deposit'] ?? 0;
        $validated['discount_percentage'] = $validated['discount_percentage'] ?? 0;

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('success', '✅ Alat berhasil diupdate!');
    }

    /**
     * Admin/Staff - Destroy
     */
    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }
        
        $item->delete();
        
        return redirect()->route('items.index')
            ->with('success', '✅ Alat berhasil dihapus!');
    }
}