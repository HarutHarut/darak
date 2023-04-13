<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Class City
 * @package App\Models
 * @property  integer $id
 * @property  string $name
 * @property  string $lat
 * @property  string $lng
 * @property  string $logo
 * @property  integer $status
 * @property  integer $top
 * @property  Media|array $media
 * @property  Media $mainMedia
 */
class City extends Model
{
    use HasFactory;

    protected $casts = [
        'about_city' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
    ];

    protected $fillable = [
        'name',
        'slug',
        'lat',
        'lng',
        'top',
        'logo',
        'preview',
        'about_city',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'related');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'city_id');
    }
}
