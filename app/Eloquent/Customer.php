<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $phone
 * @property string $telegram_id
 * @property string $username
 * @property integer $user_id
 * @property User $user
 * @property CustomerFilter[] $filters
 * @property CustomerCred[] $creds
 * @property CustomerCred $cred
 * @property string $created_at
 * @property string $updated_at
 */
class Customer extends Model
{
    protected $table = 'customer';
    protected $fillable = ['phone', 'telegram_id', 'username', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function filters(): HasMany
    {
        return $this->hasMany(CustomerFilter::class);
    }

    /**
     * @return HasMany
     */
    public function creds(): HasMany
    {
        return $this->hasMany(CustomerCred::class);
    }

//    public function scopeCred(Builder $query, string $spotType): Builder
//    {
//        return $query->where('spot_type', '=', $spotType);
//    }

}
