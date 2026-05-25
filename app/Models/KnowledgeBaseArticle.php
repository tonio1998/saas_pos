<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = [

        'title',

        'slug',

        'category',

        'content',

        'status',

        'views',

        'featured',

        'created_by',

        'updated_by',
    ];

    protected $casts = [

        'featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($article) {

            if (!$article->slug) {

                $article->slug = Str::slug(
                    $article->title
                );
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function updater()
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }
}
