<?php

namespace App\Eloquent;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property string $answer
 * @property integer $customer_id
 * @property integer $question_id
 * @property string $created_at
 * @property string $updated_at
 * @property Question $filter
 */
class CustomerAnswer extends Model
{
    protected $table = 'customer_answer';
    protected $fillable = ['answer', 'question_id', 'customer_id'];

    /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

}
