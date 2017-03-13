<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Closure;
use Dingo\Api\Routing\Router;
use Dingo\Api\Auth\Auth as Authentication;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class ApiCheckJwtMiddleware
 */
class ApiAllowAuthAndNonAuthMiddleware
{
    /**
     * Router instance.
     *
     * @var \Dingo\Api\Routing\Router
     */
    protected $router;

    /**
     * Authenticator instance.
     *
     * @var \Dingo\Api\Auth\Auth
     */
    protected $auth;

    /**
     * Create a new auth middleware instance.
     *
     * @param \Dingo\Api\Routing\Router $router
     * @param \Dingo\Api\Auth\Auth      $auth
     *
     * @return void
     */
    public function __construct(Router $router, Authentication $auth)
    {
        $this->router = $router;
        $this->auth = $auth;
    }

    /**
     * Perform authentication before a request is executed.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $route = $this->router->getCurrentRoute();

            if (! $this->auth->check(false)) {
                $this->auth->authenticate($route->getAuthenticationProviders());
            }
        }
        catch (UnauthorizedHttpException $e){}

        return $next($request);
    }
}