<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Claims;

use View;
use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Claim\ToggleShippingMethodOnClaimCommand;

/**
 * Class ClaimsApiController
 * @package Kabooodle\Http\Controllers\Api\Claims
 */
class ClaimsApiController extends AbstractApiController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->getUser()->shippingQueue;

        return $this->collection($data);
    }

    /**
     * @param Request $request
     * @param int     $claimId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function switchShippingMethod(Request $request, int $claimId)
    {
        try {
            $sale = $this->dispatchNow(new ToggleShippingMethodOnClaimCommand(
                $this->getUser(),
                $claimId,
                Binput::get('method')
            ));
            $html = View::make('sales.partials._salesrow', compact('sale'))->render();

            return $this->setData($html)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }
}
