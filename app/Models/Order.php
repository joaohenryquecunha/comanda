<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['name_client', 'status', 'cellphone'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'orders_product')->withPivot('quantity');
    }

    public function getTotalAttribute()
    {
        return $this->products->sum(function($products) {
            return $products->price * $products->pivot->quantity;
        });
    }

    public function getTotalItensAttribute()
    {
        return $this->products->sum('pivot.quantity');
    }
}

