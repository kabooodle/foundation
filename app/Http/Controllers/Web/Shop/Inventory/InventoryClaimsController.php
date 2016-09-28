<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Shop\Inventory;

use Binput;
use Response;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Kabooodle\Bus\Commands\Claim\AcceptClaimForInventoryItemCommand;
use Kabooodle\Bus\Commands\Claim\RejectClaimForInventoryItemCommand;

/**
 * Class InventoryClaimsController
 * @package Kabooodle\Http\Controllers\Web\Shop\Inventory
 */
class InventoryClaimsController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request, $username)
    {
        $data = user()->claimsOnMyInventory;

        $page = $request->get('page', 1);
        $perPage = config('pagination.per-page');

        $data = new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            count($data),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->view('inventory.claims.index')->with(compact('data'));
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function show($username, $idAndName)
    {
        //
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
        $data = user()->claimsOnMyInventory;
        $item = $data->filter(function ($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();

        if ($item) {
            $timestamp = Binput::get('accepted_on', false) ? Carbon::createFromTimestamp(strtotime(Binput::get('accepted_on'))) : null;
            $result = $this->dispatchNow(new AcceptClaimForInventoryItemCommand(
                user(),
                $claimsUUID,
                Binput::get('accepted_price', null),
                $timestamp,
                Binput::get('text', null)
            ));

            return Response::json([
                'html' => $this->view('inventory.claims.partials._claimrow')->with('claim', $result)->render()
            ], 200);
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
        $data = user()->claimsOnMyInventory;
        $item = $data->filter(function ($item) use ($claimsUUID) {
            return $item->uuid == $claimsUUID;
        })->first();

        if ($item) {
            $result = $this->dispatchNow(new RejectClaimForInventoryItemCommand(user(), $claimsUUID,
                Binput::get('rejected_reason', null)));

            return Response::json([
                'html' => $this->view('inventory.claims.partials._claimrow')->with('claim', $result)->render()
            ], 200);
        }

        return Response::json([], 401);
    }
}
