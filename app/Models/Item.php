<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'description',
        'stock_total',
        'stock_available',
        'condition',
        'image',
        'is_active',
        // ✅ TAMBAHAN: Price Fields
        'rental_price',
        'member_price',
        'late_fee',
        'deposit',
        'has_discount',
        'discount_percentage',
        'discount_start',
        'discount_end',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_discount' => 'boolean',
        'rental_price' => 'decimal:2',
        'member_price' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'deposit' => 'decimal:2',
        'discount_percentage' => 'integer',
        'discount_start' => 'date',
        'discount_end' => 'date',
    ];

    // Relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // ✅ Check if discount is active
    public function hasActiveDiscount()
    {
        if (!$this->has_discount) {
            return false;
        }

        $today = Carbon::today();
        
        if ($this->discount_start && $today->lt($this->discount_start)) {
            return false;
        }

        if ($this->discount_end && $today->gt($this->discount_end)) {
            return false;
        }

        return true;
    }

    // ✅ Get final price
    public function getFinalPrice($isMember = false)
    {
        $basePrice = $isMember && $this->member_price > 0 ? $this->member_price : $this->rental_price;

        if ($this->hasActiveDiscount()) {
            $discount = $basePrice * ($this->discount_percentage / 100);
            return $basePrice - $discount;
        }

        return $basePrice;
    }

    // ✅ Calculate late fee
    public function calculateLateFee($daysLate)
    {
        if ($daysLate <= 0) {
            return 0;
        }

        return $this->late_fee * $daysLate;
    }
}