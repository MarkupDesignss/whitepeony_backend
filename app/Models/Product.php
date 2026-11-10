<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Product extends Model
{
    protected $guarded = [];

    protected $table = 'products';

    //  protected $casts = [
    //     'category_id' => 'array',
    // ];

    public function pricings()
    {
        $user = Auth::guard('sanctum')->user();
        if(isset($user)){   
                $tierId = $user->tier_id;
                return $this->hasOne(ProductPrice::class,'product_id')->where('tier_id', $tierId);
            }else{
                return $this->hasMany(ProductPrice::class,'product_id');
            }
    }
    
    public function variants()
    {
        return $this->hasMany(ProductVariant::class,'product_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class,'product_id');
    }

        public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
    
    public function bigSale()
    {
        return $this->hasOne(BigSale::class, 'product_id')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now());
    }
    
}