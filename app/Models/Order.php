<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'product_id', 'user_id', 'status', 'reject_reason', 
        'order_code', 'count', 'totalPrice', 'tax_amount', 'discount_amount'
    ];

    protected $casts = [
        'totalPrice' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'count' => 'integer',
    ];

    // Order status constants
    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_DELIVERED = 3;
    const STATUS_CANCELLED = 4;

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function paySlipHistory()
    {
        return $this->hasOne(PaySlipHistory::class, 'order_code', 'order_code');
    }

    // Helper methods
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_CONFIRMED => 'Confirmed',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_DELIVERED => 'Delivered',
            self::STATUS_CANCELLED => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getTotalWithTaxAttribute()
    {
        return $this->totalPrice + $this->tax_amount - $this->discount_amount;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
