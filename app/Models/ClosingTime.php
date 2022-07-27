<?php

namespace App\Models;

use App\Luglocker\Builders\ClosingTimeQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property int $id
 * @property int $branch_id
 * @property string $start
 * @property string $end
 */
class ClosingTime extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'start',
        'end'
    ];

    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :ClosingTimeQueryBuilder
    {
        return new ClosingTimeQueryBuilder($query);
    }
}
