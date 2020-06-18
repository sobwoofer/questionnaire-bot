<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Service
 * @package App\Eloquent
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string $description
 * @property int $price
 * @property string $created_at
 * @property string $updated_at
 */
class Service extends Eloquent
{
    protected $table = 'service';
    protected $fillable = ['title', 'image', 'description', 'price'];

    /**
     * @return HasMany
     */
    public function service(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
