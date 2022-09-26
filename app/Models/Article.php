<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/articles';

    public const SMALL = '135x141';
	public const MEDIUM = '312x400';
	public const LARGE = '1000x600';

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categoryArticles()
    {
        return $this->belongsToMany('App\Models\CategoryArticle', 'article_categories');
    }
}
