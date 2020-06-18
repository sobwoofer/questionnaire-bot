<?php

namespace App\Eloquent;

use Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ServiceTag
 * @package App\Eloquent
 * @property int $tag_id
 * @property int $service_id
 */
class ServiceTag extends Eloquent
{
    protected $table = 'service_tag';

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
        return $this->belongsTo(Service::class, 'service_id');
    }
}
