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

    protected $fillable = [
        'name',
        'lat',
        'lng',
        'top',
        'logo'
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'related');
    }
}
