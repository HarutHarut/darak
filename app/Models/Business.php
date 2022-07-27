<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $lat
 * @property string $lng
 * @property string $phone
 * @property string $address
 * @property string $logo
 * @property int $rating
 * @property int $status
 * @property int $publish
 * @property string $timezone
 * @property User $user
 * @property Branch $branches
 * @property Size $sizes
 * @property SocialNetworkUrl $socialNetworkUrl
 */

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'lat',
        'lng',
        'phone',
        'email',
        'country_code',
        'address',
        'rating',
        'status',
        'timezone',
        'currency'
    ];

    protected $casts = [
//        'description' => 'array',
        'name' => 'array',
    ];

    /** User
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Branch
     * @return HasMany
     */

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /** Size
     * @return HasMany
     */

    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class);
    }

    /** SocialNetworkUrl
     * @return HasMany
     */

    public function socialNetworkUrl(): HasMany
    {
        return $this->hasMany(SocialNetworkUrl::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function invoices(){
        return $this->hasMany(Invoices::class);
    }
}
