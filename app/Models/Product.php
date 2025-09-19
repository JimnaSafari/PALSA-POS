<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'price', 'category_id', 'description', 
        'count', 'image', 'purchase_price', 'sku', 'barcode'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'count' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Business logic methods
    public function getAvailableStockAttribute()
    {
        $soldQuantity = $this->orders()
            ->where('status', 1) // Confirmed orders
            ->sum('count');
        
        return max(0, $this->count - $soldQuantity);
    }

    public function getProfitMarginAttribute()
    {
        if ($this->purchase_price > 0) {
            return (($this->price - $this->purchase_price) / $this->purchase_price) * 100;
        }
        return 0;
    }

    public function isInStock($quantity = 1)
    {
        return $this->available_stock >= $quantity;
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('count', '>', 0);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
