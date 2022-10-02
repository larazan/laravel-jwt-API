<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/movies';

    public const SMALL = '135x141';
	public const MEDIUM = '312x400';
    public const LARGE = '600x656';

    public function category()
    {
        return $this->hasOne(CategoryMovie::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function networks()
    {
        return $this->belongsToMany(Network::class);
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class);
    }

    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function persons()
    {
        return $this->belongsToMany(Person::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tags()
    {
        return $this->morphToMany('App\Models\Tag', 'taggable');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
}
