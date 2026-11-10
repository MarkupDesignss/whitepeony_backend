<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'icon',
        'image',
        'category_type',
        'position',
        'description',
    ];
    
}