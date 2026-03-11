<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'borrow_date',
        'return_date',
        'actual_return_date',
        'status',
        'notes',
        'approved_by',
        // ✅ NEW FIELDS
        'rental_price_per_day',
        'total_days',
        'subtotal',
        'discount_code',
        'discount_amount',
        'total_after_discount',
        'late_fee',
        'deposit_paid',
        'grand_total',
        'payment_status',
        'payment_method',
        'paid_at',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
        'rental_price_per_day' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_after_discount' => 'decimal:2',
        'late_fee' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ✅ Calculate total days
    public function calculateTotalDays()
    {
        $borrowDate = Carbon::parse($this->borrow_date);
        $returnDate = Carbon::parse($this->return_date);
        
        return $borrowDate->diffInDays($returnDate) + 1;
    }

    // ✅ Calculate late days
    public function calculateLateDays()
    {
        if (!$this->actual_return_date) {
            $today = Carbon::today();
            $returnDate = Carbon::parse($this->return_date);
            
            if ($today->gt($returnDate)) {
                return $returnDate->diffInDays($today);
            }
            
            return 0;
        }

        $actualReturn = Carbon::parse($this->actual_return_date);
        $returnDate = Carbon::parse($this->return_date);
        
        if ($actualReturn->gt($returnDate)) {
            return $returnDate->diffInDays($actualReturn);
        }

        return 0;
    }

    // ✅ Calculate total price
    public function calculateTotalPrice($discountCode = null)
    {
        $item = $this->item;
        $isMember = $this->user->role === 'user'; // Bisa disesuaikan
        
        // Harga per hari
        $pricePerDay = $item->getFinalPrice($isMember);
        
        // Total hari
        $totalDays = $this->calculateTotalDays();
        $this->total_days = $totalDays;
        
        // Subtotal
        $subtotal = $pricePerDay * $totalDays;
        $this->subtotal = $subtotal;
        
        // Diskon
        $discountAmount = 0;
        if ($discountCode) {
            $discount = Discount::where('code', $discountCode)->first();
            if ($discount && $discount->isValid()) {
                $discountAmount = $discount->calculateDiscount($subtotal);
                $discount->incrementUsage();
            }
        }
        
        $this->discount_code = $discountCode;
        $this->discount_amount = $discountAmount;
        $this->total_after_discount = $subtotal - $discountAmount;
        
        // Deposit
        $this->deposit_paid = $item->deposit;
        
        // Grand Total
        $this->grand_total = $this->total_after_discount + $this->deposit_paid;
        
        return [
            'price_per_day' => $pricePerDay,
            'total_days' => $totalDays,
            'subtotal' => $subtotal,
            'discount' => $discountAmount,
            'total_after_discount' => $this->total_after_discount,
            'deposit' => $this->deposit_paid,
            'grand_total' => $this->grand_total,
        ];
    }
}