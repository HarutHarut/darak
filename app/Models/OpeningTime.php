<?php

namespace App\Models;

use App\Luglocker\Builders\OpeningTimeQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $branch_id
 * @property int $weekday
 * @property string $start
 * @property string $end
 * @property string $status
 */
class OpeningTime extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'weekday',
        'start',
        'end',
        'status'
    ];

    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :OpeningTimeQueryBuilder
    {
        return new OpeningTimeQueryBuilder($query);
    }
}
