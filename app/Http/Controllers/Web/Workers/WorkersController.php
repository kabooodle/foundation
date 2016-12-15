<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Workers;

use Artisan;
use Exception;
use InvalidArgumentException;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class WorkersController
 */
class WorkersController extends Controller
{
    /**
     * @param $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fb($key)
    {
        try {
            if(! $key || $key <> '7AF95578E9A597AA6B89E726E74C4') {
                throw new InvalidArgumentException('nope');
            }
            $response = Artisan::call('facebook:enqueue');
        } catch (Exception $e) {
            $response = $e->getMessage();
        }

        return response()->json([$response]);
    }

    /**
     * @param $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checktrials($key)
    {
        return response()->json([]);
    }
}