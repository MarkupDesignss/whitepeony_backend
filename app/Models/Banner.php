<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $fillable = [
    'title','image_url','status','start_date','end_date',
    'product_type','product_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}