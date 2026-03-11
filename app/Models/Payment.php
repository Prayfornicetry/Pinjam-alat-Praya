<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'transaction_id',
        'amount',
        'payment_method',
        'status',
        'payment_proof',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }
}