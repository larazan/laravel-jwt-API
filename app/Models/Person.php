<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    // protected $guarded = [
	// 	'id',
	// 	'created_at',
	// 	'updated_at',
	// ];

    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/persons';

	public const SMALL = '135x141';
	public const MEDIUM = '312x400';
	public const LARGE = '600x656';

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }

    public function musics()
    {
        return $this->hasMany(Music::class);
    }

    

    public function characters()
    {
        return $this->belongsToMany(Character::class);
    }
}
