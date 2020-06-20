<?php

namespace App\Eloquent;

use App\User;
use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property string $url
 * @property string $title
 * @property string $description
 * @property string $image
 * @property integer $filter_id
 * @property string $created_at
 * @property string $updated_at
 */
class CustomerItem extends Model
{
    protected $table = 'customer_item';
    protected $fillable = ['url', 'title', 'description', 'image', 'filter_id'];

    /**
     * @return BelongsTo
     */
    public function filter(): BelongsTo
    {
        return $this->belongsTo(CustomerFilter::class);
    }

}
