<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class PhotoTag
 * @package App\Eloquent
 * @property int $tag_id
 * @property int $post_id
 */
class PhotoTag extends Eloquent
{
    protected $table = 'photo_tag';

    /**
     * @return BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    /**
     * @return BelongsTo
     */
    public function photo(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'photo_id');
    }
}
