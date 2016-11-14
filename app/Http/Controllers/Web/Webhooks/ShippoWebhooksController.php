<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class ShippoWebhooksController
 * @package Kabooodle\Http\Controllers\Web\Webhooks
 */
class ShippoWebhooksController extends Controller
{
    /**
     * @param Request $request
     */
    public function handleWebhook(Request $request)
    {
        if (!$request->has('x') || $request->get('x') <> 'KuMQnR5hhzlM2Wk7q9aS') {
            return;
        }

        $payload = [
            
        ];
    }
}
