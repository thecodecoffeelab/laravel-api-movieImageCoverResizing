<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovieManipulation extends Model
{
    use HasFactory;

    //constant to resize cover image movie
    const TYPE_RESIZE = 'resize';

    const UPDATED_AT = null;

    protected $fillable = ['name', 'path', 'type', 'data', 'output_path', 'user_id', 'movie_id'];
}
