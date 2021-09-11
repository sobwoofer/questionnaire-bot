<?php

namespace App\Eloquent;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Customer
 * @package App\Eloquent
 * @property integer $id
 * @property string $phone
 * @property string $chat_id
 * @property string $username
 * @property string $state
 * @property string $first_name
 * @property string $last_name
 * @property string $language
 * @property integer $update_id
 * @property integer $user_id
 * @property integer $answer_state
 * @property User $user
 * @property CustomerAnswer[] $customerAnswers
 * @property string $created_at
 * @property string $updated_at
 */
class Customer extends Model
{
    public const STATE_START = 'start';
    public const STATE_CHOOSING_LANGUAGE = 'choosingLanguage';
    public const STATE_ANSWERING = 'answering';
    public const STATE_FINISHED = 'finished';
    public const STATE_ASKED_AGAIN = 'askedAgain';

    public const LANG_EN = 'en';
    public const LANG_RU = 'ru';

    protected $table = 'customer';
    protected $fillable = [
        'phone',
        'chat_id',
        'state',
        'username',
        'update_id',
        'first_name',
        'last_name',
        'language',
        'answer_state',
        'user_id'
    ];

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
    public function customerAnswers(): HasMany
    {
        return $this->hasMany(CustomerAnswer::class);
    }

    public function setState(string $state): bool
    {
        $this->state = $state;
        return $this->save();
    }

    public function setAnswerState(?int $state): bool
    {
        $this->answer_state = $state;
        return $this->save();
    }

    public function setLang(string $lang): bool
    {
        $this->language = $lang;
        return $this->save();
    }

    public function setUpdateId(string $updateId): bool
    {
        $this->update_id = $updateId;
        return $this->save();
    }

}
