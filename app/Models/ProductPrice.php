<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $guarded = [];

    protected $table = 'product_pricing';

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'tier_id');
    }
}