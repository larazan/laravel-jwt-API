<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    use HasFactory;

    protected $guarded = [
		'id',
		'created_at',
		'updated_at',
	];

	public const UPLOAD_DIR = 'uploads/networks';

	public const EXTRA_LARGE = '1920x643';
    public const MEDIUM = '312x400';
	public const SMALL = '135x75';

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
