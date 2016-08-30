<?php

namespace Kabooodle\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

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
        \Kabooodle\Http\Middleware\IfTurbolinksMiddleware::class
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
            \Kabooodle\Http\Middleware\VerifyCsrfToken::class,
            \Kabooodle\Http\Middleware\ResponseHeadersMiddleware::class
//            \Kabooodle\Http\Middleware\FilterIfPjax::class,
//            \Kabooodle\Http\Middleware\AccountActive::class,
        ],

        'api' => [
            \Barryvdh\Cors\HandleCors::class,
            'throttle:60,1',
        ],
    ];

    /**
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Kabooodle\Http\Middleware\Authenticate::class,
        'cors' =>             \Barryvdh\Cors\HandleCors::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'can' => \Illuminate\Foundation\Http\Middleware\Authorize::class,
        'guest' => \Kabooodle\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        'jwt.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
        'jwt.renew' => \Tymon\JWTAuth\Http\Middleware\AuthenticateAndRenew::class,
    ];
}
