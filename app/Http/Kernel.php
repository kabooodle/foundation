<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Kabooodle\Http\Middleware\ReferralProgramMiddleware;

/**
 * Class Kernel
 * @package Kabooodle\Http
 */
class Kernel extends HttpKernel
{
    /**
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Kabooodle\Http\Middleware\IfTurbolinksMiddleware::class,
//        \Barryvdh\Cors\HandleCors::class
    ];

    /**
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \Kabooodle\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Kabooodle\Http\Middleware\ReferralProgramMiddleware::class,
            \Kabooodle\Http\Middleware\VerifyCsrfToken::class,
            \Kabooodle\Http\Middleware\ResponseHeadersMiddleware::class,
//            \PragmaRX\Tracker\Vendor\Laravel\Middlewares\Tracker::class,
//            \Kabooodle\Http\Middleware\FilterIfPjax::class,
//            \Kabooodle\Http\Middleware\AccountActive::class,
        ],

        'api' => [
//            \Barryvdh\Cors\HandleCors::class,
            'throttle:60,1',
        ],
    ];

    /**
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Kabooodle\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        'guest' => \Kabooodle\Http\Middleware\RedirectIfAuthenticated::class,
        'subscribed' => \Kabooodle\Http\Middleware\Subscribed::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        'jwt.renew' => \Tymon\JWTAuth\Http\Middleware\AuthenticateAndRenew::class,
    ];
}
