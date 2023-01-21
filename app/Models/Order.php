<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        //delete all products related to order
        static::deleted(function($order){
            $order->order_products()->delete();
        });
    }

    public function order_products()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderAmount()
    {
        $products = $this->order_products;
        $total = 0;
        foreach($products as $product){
            $total += $product->total;
        }

        return $total;
    }
}
