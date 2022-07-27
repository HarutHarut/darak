<?php

namespace App\Models;

use App\Luglocker\Builders\LockerQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $branch_id
 * @property string $name
 * @property int $count
 * @property float $price_per_hour
 * @property float $price_per_day
 * @property int $working_status
 * @property string $verified
 * @property Size $size
 * @property Price $prices
 * @property Booking $booking
 */

class Locker extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'array',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'branch_id',
        'size_id',
        'name',
        'count',
        'price_per_hour',
        'price_per_day',
        'working_status',
        'verified'
    ];

    /** Size
     * @return BelongsTo
     */

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }

    /** Price
     * @return HasMany
     */

    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /** minLockerPrice
     * @return HasMany
     */

    public function minLockerPrices()
    {
        return $this->prices()->where('price','>=',0)->min('price');
    }

    /** Booking
     * @return HasMany
     */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** Branch
     * @return BelongsTo
     */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }


    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :LockerQueryBuilder
    {
        return new LockerQueryBuilder($query);
    }
    public function closeTime(): HasMany
    {
        return $this->hasMany(SpecialClosingTime::class);
    }
}
