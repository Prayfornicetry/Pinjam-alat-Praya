<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'min_transaction',
        'max_discount',
        'usage_limit',
        'usage_count',
        'start_date',
        'end_date',
        'is_active',
        'description',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_transaction' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ✅ Check if discount is valid
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();

        if ($today->lt($this->start_date) || $today->gt($this->end_date)) {
            return false;
        }

        if ($this->usage_limit > 0 && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // ✅ Calculate discount amount
    public function calculateDiscount($subtotal)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($subtotal < $this->min_transaction) {
            return 0;
        }

        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);
            
            if ($this->max_discount && $discount > $this->max_discount) {
                return $this->max_discount;
            }

            return $discount;
        }

        return $this->value;
    }

    // ✅ Increment usage count
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }
}