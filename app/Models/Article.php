<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasTranslations;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'image_path',
        'category',
        'author_id',
        'published_at',
    ];

    protected $translatable = [
        'title',
        'excerpt',
        'content',
    ];

    protected $casts = [
        'title' => 'array',
        'excerpt' => 'array',
        'content' => 'array',
        'published_at' => 'datetime',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
