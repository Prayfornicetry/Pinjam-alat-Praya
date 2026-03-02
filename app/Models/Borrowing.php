<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'rejection_reason',
        'approved_by',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
        'actual_return_date' => 'date',
    ];

    // Relationship ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship ke Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship ke Admin yang approve
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}