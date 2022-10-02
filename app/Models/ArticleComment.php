<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ArticleComment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function childrens()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function hasLike()
    {
        return $this->hasOne(ArticleCommentLike::class)->where('article_comment_likes.user_id', Auth::user()->id);
    }

    public function totalLikes()
    {
        return $this->hasMany(ArticleCommentLike::class)->count();
    }
}
