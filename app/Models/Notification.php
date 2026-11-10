<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    
    protected $guarded =[];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}