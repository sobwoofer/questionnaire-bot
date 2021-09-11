<?php

namespace App\Providers;

use App\Events\States\Answered;
use App\Events\States\AskedAgain;
use App\Events\States\ChosenLang;
use App\Events\States\Finished;
use App\Events\States\Start;
use App\Listeners\States\AnsweredListener;
use App\Listeners\States\AskedAgainListener;
use App\Listeners\States\ChosenLangListener;
use App\Listeners\States\FinishedListener;
use App\Listeners\States\StartListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Start::class => [StartListener::class],
        ChosenLang::class => [ChosenLangListener::class],
        Answered::class => [AnsweredListener::class],
        Finished::class => [FinishedListener::class],
        AskedAgain::class => [AskedAgainListener::class],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
