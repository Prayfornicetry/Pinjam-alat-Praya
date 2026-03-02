<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'stock_total',
        'stock_available',
        'condition',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'stock_total' => 'integer',
        'stock_available' => 'integer',
    ];

    // Relationship ke Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship ke Borrowings
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }
}