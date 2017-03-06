<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Services\DateFactory;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForClaimableItemCommand;
use Kabooodle\Bus\Commands\Claim\RejectClaimForClaimableItemCommand;

/**
 * Class InventoryClaimsController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryClaimsController extends Controller
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
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, $username)
    {
        $data = webUser()->pendingClaimsOnMyListables;
        $data = $this->paginateData($request, $data);

        return $this->view('inventory.claims.index')->with(compact('data'));
    }

    /**
     * @param Request $request
     * @param         $username
     * @param         $claimsUUID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $username, $claimsUUID)
    {
        $data = webUser()->claimsOnMyListables();
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
     * @param         $username
     * @param         $claimsUUID
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $username, $claimsUUID)
    {
        $data = webUser()->claimsOnMyInventory;
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
