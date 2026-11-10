<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Article extends Model
{
    use HasFactory;
    protected $table = 'articles';
    protected $fillable = [
        'author_id', 'title', 'slug', 'content', 'image', 'status', 'views'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function images()
{
    return $this->hasMany(ArticleImage::class);
}
}