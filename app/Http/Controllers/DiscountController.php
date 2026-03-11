<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display discounts (Admin Only)
     */
    public function index()
    {
        $discounts = Discount::latest()->paginate(10);
        return view('discounts.index', compact('discounts'));
    }

    /**
     * Create discount form
     */
    public function create()
    {
        return view('discounts.create');
    }

    /**
     * Store discount
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|unique:discounts,code|max:50',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        Discount::create($validated);

        return redirect()->route('discounts.index')
            ->with('success', '✅ Diskon berhasil ditambahkan!');
    }

    /**
     * Edit discount
     */
    public function edit($id)
    {
        $discount = Discount::findOrFail($id);
        return view('discounts.edit', compact('discount'));
    }

    /**
     * Update discount
     */
    public function update(Request $request, $id)
    {
        $discount = Discount::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:discounts,code,' . $id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_transaction' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $discount->update($validated);

        return redirect()->route('discounts.index')
            ->with('success', '✅ Diskon berhasil diupdate!');
    }

    /**
     * Delete discount
     */
    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('discounts.index')
            ->with('success', '✅ Diskon berhasil dihapus!');
    }

    /**
     * Validate discount code (AJAX)
     */
    public function validateCode(Request $request)
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
}