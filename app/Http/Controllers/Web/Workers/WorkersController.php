<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Workers;

use Bugsnag;
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
                throw new InvalidArgumentException('Webhook key missing/invalid');
            }
            $response = Artisan::call('facebook:enqueue');
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            $response = $e->getMessage();
        }

        return response()->json([$response]);
    }

    /**
     * TODO: Setup a command that queues items for deletion.
     *
     * @param $key
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function fbDeletion($key)
    {
        try {
            if(! $key || $key <> '7AF95578E9A597AA6B89E726E74C4') {
                throw new InvalidArgumentException('Webhook key missing/invalid');
            }
            $response = Artisan::call('facebook:enqueue');
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
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
        try {
            if(! $key || $key <> '7AF95578E9A597AA6B89E726E74C4') {
                throw new InvalidArgumentException('Webhook key missing/invalid');
            }
            $response = Artisan::call('expiring:trials');
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            $response = $e->getMessage();
        }

        return response()->json([$response]);
    }
}