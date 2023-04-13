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
        'meta_keywords',
        'meta_description',
        'meta_title',
        'description_am',
        'description_ru',
        'description_en',
        'description_sp',
        'description_ch',
        'description_de',
        'description_fr',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'meta_keywords' => 'array',
        'meta_description' => 'array',
        'meta_title' => 'array',
    ];
}
