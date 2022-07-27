<?php

namespace App\Models;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticate;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $avatar
 * @property string $deleted
 * @property integer $status
 * @property string $password
 * @property Business $business
 * @property Feedback $feedbacks
 * @property Booking $bookings
 * @property Role $role
 */

class User extends Authenticate implements MustVerifyEmail
{

    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'avatar',
        'deleted',
        'status',
        'password',
        "role_id",
        'currency',
        'email_verified_at',
        'provider',
        'provider_id',
        'first_login'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /** Business
     * @return HasOne
     */

    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    /** Feedback
     * @return HasMany
     */

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    /** Booking
     * @return HasMany
     */

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class,'booker_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    /** Role
     * @return belongsTo
     */

    public function role(): belongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(){
        return $this->role()->where('name','admin')->first();
    }

    public function isBusiness(){
        return $this->role()->where('name','business_owner')->first();
    }

    public function isUser(){
        return $this->role()->where('name','user')->first();
    }


//    public function currency(){
//        return $this->belongsTo(Currency::class);
//    }
}
