<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Tag
 * @package App\Eloquent
 * @property int $id
 * @property string $tag
 * @property string $label
 * @property string $created_at
 * @property string $updated_at
 */
class Tag extends Eloquent
{
    protected $table = 'tag';
    protected $fillable = ['tag', 'label'];

    /**
     * @return HasMany
     */
    public function photoTag(): HasMany
    {
        return $this->hasMany(PhotoTag::class);
    }

    /**
     * @return HasMany
     */
    public function postTag(): HasMany
    {
        return $this->hasMany(PostTag::class);
    }

    /**
     * @return HasManyThrough
     */
    public function photos(): HasManyThrough
    {
        return $this->hasManyThrough(Photo::class, PhotoTag::class);
    }

    /**
     * @return HasManyThrough
     */
    public function posts(): HasManyThrough
    {
        return $this->hasManyThrough(Post::class, PhotoTag::class);
    }
}
