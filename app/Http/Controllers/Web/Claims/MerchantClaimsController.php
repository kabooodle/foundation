<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Claims;

use Binput;
use Response;
use Illuminate\Http\Request;
use Kabooodle\Services\DateFactory;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForClaimableItemCommand;
use Kabooodle\Bus\Commands\Claim\RejectClaimForClaimableItemCommand;

/**
 * Class MerchantClaimsController
 */
class MerchantClaimsController extends Controller
{
    use ObfuscatesIdTrait, PaginatesTrait;

    /**
     * @var DateFactory
     */
    public $dateFactory;

    /**
     * @param DateFactory $dateFactory
     */
    public function __construct(DateFactory $dateFactory)
    {
        $this->dateFactory = $dateFactory;
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return $this->view('claims.index');
    }

    /**
     * @param Request $request
     * @param         $claimsUUID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $claimsUUID)
    {
        $data = webUser()->claimsOnMyListables;
        $claim = $data->filter(function ($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();

        if ($claim) {
            $timestamp = Binput::get('accepted_on', false) ? $this->dateFactory->parse(Binput::get('accepted_on')) : null;
            $result = $this->dispatchNow(new AcceptClaimForClaimableItemCommand(
                webUser(),
                $claimsUUID,
                Binput::get('accepted_price', null),
                $timestamp,
                Binput::get('text', null)
            ));

            return Response::json('ok', 200);
        }

        return Response::json([], 401);
    }

    /**
     * @param Request $request
     * @param         $claimsUUID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $claimsUUID)
    {
        $data = webUser()->claimsOnMyListables;
        $item = $data->filter(function ($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();

        if ($item) {
            $result = $this->dispatchNow(new RejectClaimForClaimableItemCommand(webUser(), $claimsUUID,
                Binput::get('rejected_reason', null)));

            return Response::json('ok', 200);
        }

        return Response::json([], 401);
    }
}
