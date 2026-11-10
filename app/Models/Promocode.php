<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocode extends Model
{
    protected $table ='promocodes';
    protected $guarded =[];
    
    public function tier()
    {
        return $this->belongsTo(Tier::class, 'tier_id');
    }
}