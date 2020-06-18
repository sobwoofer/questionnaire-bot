<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Photo
 * @package App\Eloquent
 * @property int $id
 * @property string $image
 * @property string $title
 * @property string $created_at
 * @property string $updated_at
 */
class Photo extends Eloquent
{
    protected $table = 'photo';
    protected $fillable = ['image', 'title'];

    /**
     * @return HasManyThrough
     */
    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class,PostTag::class);
    }
}
