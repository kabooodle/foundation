<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use DB;
use Closure;
use Illuminate\Http\Request;

/**
 * Class TerminableMiddleware
 */
class TerminableMiddleware
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    /**
     * @param $request
     * @param $response
     */
    public function terminate($request, $response)
    {
        $connections = DB::getConnections();
        foreach ($connections as $connection) {
            $pdo = $connection->getPdo();
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }
        DB::purge('mysql');
    }
}