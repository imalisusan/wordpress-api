<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    public function orders()
    {
        return $this->hasMany(ProductDetail::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentDetail::class, 'order_id');
    }
}
