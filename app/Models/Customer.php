<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $casts = [
        'otp_created_at' => 'datetime',
        'email_verified_at' => 'datetime',
    ];
    
    protected $guarded =[];
    protected $table = 'customers';

    public function address()
    {
        return $this->hasOne(Address::class, 'customer_id');
    }
    
    public function tier()
    {
        return $this->belongsTo(Tier::class, 'tier_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'customer_id', 'id');
    }
}