<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPages extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'meta_keywords',
        'meta_description',
        'meta_title',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'meta_keywords' => 'array',
        'meta_description' => 'array',
        'meta_title' => 'array',
    ];
}
