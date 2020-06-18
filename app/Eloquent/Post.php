<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Class Post
 * @package App\Eloquent
 * @property int $id
 * @property string $title
 * @property string $image
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 */
class Post extends Eloquent
{
    protected $table = 'post';
    protected $fillable = ['title', 'image', 'description'];

    /**
     * @return HasManyThrough
     */
    public function tags(): HasManyThrough
    {
        return $this->hasManyThrough(Tag::class,PostTag::class);
    }

}
