<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property string $filter_url
 * @property string $filter_title
 * @property string $spot_type
 * @property string $title
 * @property boolean $enabled
 * @property integer $user_id
 * @property string $created_at
 * @property string $updated_at
 */
class CustomerFilter extends Model
{
    protected $table = 'customer_filter';
    protected $fillable = ['filter_url', 'filter_title', 'spot_type', 'title', 'enabled', 'user_id'];

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
    public function items(): HasMany
    {
        return $this->hasMany(CustomerItem::class);
    }
}
