<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $guarded =[];
    protected $table ='wishlists';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->with('variants');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}