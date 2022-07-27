<?php

namespace App\Models;

use App\Luglocker\Builders\BookingQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $booker_id
 * @property int $branch_id
 * @property int $locker_id
 * @property string $start
 * @property string $end
 * @property float $amount
 * @property string $status
 * @property Branch $branch
 * @property Locker $locker
 * @property User $user
 */

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'booking_number',
        'booker_id',
        'branch_id',
        'locker_id',
        'start',
        'end',
        'amount',
        'status',
    ];


    /** Branch
     * @return BelongsTo
     */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class,'booking_number', 'booking_number');
    }

    /** Locker
     * @return BelongsTo
     */

    public function locker(): BelongsTo
    {
        return $this->belongsTo(Locker::class);
    }

    /** User
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'booker_id');
    }

    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :BookingQueryBuilder
    {
        return new BookingQueryBuilder($query);
    }
}
