<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Log;

class TelegramController extends BaseController
{
    public function index(): void
    {
        Log::info('have a new request');
        $a = '';
    }
}
