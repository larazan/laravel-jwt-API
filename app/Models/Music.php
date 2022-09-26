<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Music extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/musics';
    public const UPLOAD_AUDIO = 'uploads/files/audios';

    public const SMALL = '135x141';
	public const MEDIUM = '312x400';

    public function person()
    {
        return $this->belongsTo(Person::class);
    }
}
