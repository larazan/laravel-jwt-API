<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Character extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function person()
    {
        return $this->belongsToMany(Person::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
