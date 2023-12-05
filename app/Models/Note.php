<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;
    protected $fillable=[
        'path_logo',
        'name',
        'slug',
        'descr',
        'post_md',
        'post_js',
    ];
}
