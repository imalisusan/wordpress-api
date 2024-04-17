<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 
        'name', 
        'quantity',
        'total_price',
        'product'
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerDetail::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductDetail::class, 'product_id');
    }
}
