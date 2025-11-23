<?php

namespace App\Http\Middleware;

use Illuminate\Session\Middleware\StartSession as Middleware;

class StartSession extends Middleware
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
