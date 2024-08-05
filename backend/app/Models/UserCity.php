<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCity extends Model
{
    use HasFactory;

    // Если хотите, можете указать поля, которые можно массово заполнять
    protected $fillable = [
        'user_id',
        'city_id',
    ];
}

