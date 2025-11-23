<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse as Middleware;

class AddQueuedCookiesToResponse extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
}