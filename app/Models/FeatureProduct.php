<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureProduct extends Model
{
    protected $table = 'feature_products';
    protected $fillable = [
        'product_id','product_type','status'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
