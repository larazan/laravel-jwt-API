<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    use HasFactory;

    protected $guarded = [];

    public const UPLOAD_DIR = 'uploads/podcasts';
    public const UPLOAD_AUDIO = 'uploads/files/podcasts';

    public const SMALL = '135x141';
	public const MEDIUM = '312x400';
}
