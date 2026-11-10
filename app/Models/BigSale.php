<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BigSale extends Model
{
    protected $table = 'big_sales';
    protected $fillable = [
        'product_type','product_id','start_date','end_date','percentage','status'
    ];

    protected $casts = [
        'product_ids' => 'array'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
