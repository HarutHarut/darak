<?php

namespace App\Models;

use App\Luglocker\Builders\BranchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * @property int $id
 * @property int $business_id
 * @property string $currency_id
 * @property string $city_id
 * @property string $name
 * @property string $lat
 * @property string $lng
 * @property string $logo
 * @property string $phone
 * @property string $country
 * @property string $address
 * @property string $description
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property string $status
 * @property string $working_status
 * @property int $card_payment
 * @property string $verified
 * @property Business $business
 * @property City $city
 * @property Locker $lockers
 * @property Currency $currency
 * @property Feedback $feedbacks
 * @property ClosingTime $closingTimes
 * @property OpeningTime $openingTimes
 * @property Booking $bookings
 */

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'description' => 'array',
        'name' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'meta_keywords' => 'array',
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'city_id',
        'currency_id',
        'name',
        'slug',
        'lat',
        'lng',
        'logo',
        'phone',
        'country_code',
        'email',
        'country',
        'address',
        'status',
        'working_status',
        'card_payment',
        'is_bookable',
        'verified',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'recommended'
    ];



    /** City
     * @return BelongsTo
     */

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /** Currency
     * @return BelongsTo
     */

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /** Business
     * @return BelongsTo
     */

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /** Feedback
     * @return HasMany
     */

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /** Locker
     * @return HasMany
     */

    public function lockers(): HasMany
    {
        return $this->hasMany(Locker::class);
    }

    /** ClosingTime
     * @return HasMany
     */

    public function closingTimes(): HasMany
    {
        return $this->hasMany(ClosingTime::class);
    }

    /** OpeningTime
     * @return HasMany
     */

    public function openingTimes(): HasMany
    {
        return $this->hasMany(OpeningTime::class);
    }

    /** Booking
     * @return HasMany
     */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /** SocialNetworkUrl
     * @return HasMany
     */

    public function socialNetworkUrls(): HasMany
    {
        return $this->hasMany(SocialNetworkUrl::class);
    }

    /**
     * Get the Branch's media.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'related');
    }

    /**
     * Get the Branch's main Media.
     */
    public function mainMedia(): MorphOne
    {
        return $this->morphOne(Media::class, 'related')
            ->where('main', '=', true);
    }

    public static function query() : Builder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query) :BranchQueryBuilder
    {
        return new BranchQueryBuilder($query);
    }
}
