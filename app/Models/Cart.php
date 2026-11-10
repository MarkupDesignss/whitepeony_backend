<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];
    protected $table = 'carts';
    
        public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }
}