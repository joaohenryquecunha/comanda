<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'price'];

    public function order()
    {
        return $this->belongsToMany(Order::class, 'orders_product')->withPivot('quantity');
    }
}

