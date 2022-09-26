<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/episodes';

	public const SMALL = '135x141';
	public const MEDIUM = '312x400';
	public const LARGE = '600x656';

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
