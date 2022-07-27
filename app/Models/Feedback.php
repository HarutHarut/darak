<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property int $user_id
 * @property int $branch_id
 * @property int $book_id
 * @property string $text
 * @property string $rating
 * @property int $status
 * @property Branch $branch
 * @property User $author
 */
class Feedback extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'branch_id',
        'order_id',
        'text',
        'rating',
        'status'
    ];


    /** Branch
     * @return BelongsTo
     */

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** User
     * @return BelongsTo
     */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
