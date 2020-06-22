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
 * @property string $chat_id
 * @property string $username
 * @property string $state
 * @property string $first_name
 * @property string $last_name
 * @property integer $update_id
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
    public const START_STATE = 'start';
    public const ADD_FILTER_STATE = 'addFilter';
    public const HUNTING_STATE = 'hunting';
    public const REMOVE_FILTER_STATE = 'removeFilter';
    public const RUN_FILTER_STATE = 'runFilter';
    public const SHOW_FILTERS_STATE = 'showFilters';
    public const STOP_FILTER_STATE = 'showFilters';

    protected $table = 'customer';
    protected $fillable = ['phone', 'chat_id', 'state', 'username', 'update_id', 'first_name', 'last_name', 'user_id'];

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

    /**
     * @param string $state
     * @return bool
     */
    public function setState(string $state): bool
    {
        $this->state = $state;
        return $this->save();
    }

    public function setUpdateId(string $updateId): bool
    {
        $this->update_id = $updateId;
        return $this->save();
    }

//    public function scopeCred(Builder $query, string $spotType): Builder
//    {
//        return $query->where('spot_type', '=', $spotType);
//    }

}
