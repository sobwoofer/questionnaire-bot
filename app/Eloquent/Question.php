<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class CustomerFilter
 * @package App\Eloquent
 * @property integer $id
 * @property string $question_ru
 * @property string $question_en
 * @property int $position
 */
class Question extends Model
{
    public const ROLE_DIRECTION = 'direction';
    public const ROLE_QUESTION = 'question';
    public const ROLE_FINAL = 'final';
    public const ROLE_DOC = 'doc';

    protected $table = 'question';
    protected $fillable = ['question_ru', 'question_en', 'position'];

    /**
     * @return HasMany
     */
    public function customerAnswers(): HasMany
    {
        return $this->hasMany(CustomerAnswer::class);
    }

}
