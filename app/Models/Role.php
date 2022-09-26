<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

	public const ACTIVE = 1;
	public const INACTIVE = 2;

	public const STATUSES = [
		self::ACTIVE => 'active',
		self::INACTIVE => 'inactive',
	];

    public static function statuses()
	{
		return self::STATUSES;
	}

	public function character()
    {
        return $this->hasMany(Character::class);
    }

}
