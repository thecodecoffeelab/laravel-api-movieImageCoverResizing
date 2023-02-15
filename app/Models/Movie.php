<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;
    
    //Allow mass assignement to get value name stored
    protected $fillable = ['name', 'user_id'];
}
