<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Address\AddAddressCommand;
use Kabooodle\Bus\Commands\Address\DestroyAddressCommand;
use Kabooodle\Bus\Commands\Address\MakeAddressPrimaryCommand;
use Kabooodle\Bus\Commands\Address\ResendAddressVerificationCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\Address;

/**
 * Class AddressController
 * @package Kabooodle\Http\Controllers\Api\User
 */
class AddressController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $addresses = Address::whereUserId($this->getUser()->id)->get();

            return $this->setData(['addresses' => $addresses])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond($e);
        }
    }

    /**
     * @param Request $request
     * @param $userId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        try {
            $this->validate($request, Address::getRules());

            $address = $this->dispatchNow(new AddAddressCommand(
                $this->getUser(),
                $request->get('type'),
                $request->get('primary', false),
                $request->get('company'),
                $request->get('street1'),
                $request->get('street2'),
                $request->get('city'),
                $request->get('state'),
                $request->get('zip'),
                $request->get('phone')));

            return $this->setData(['address' => $address])->respond();
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function update(Request $request, $userId, $addressId)
    {
        try {
            $address = Address::whereId($addressId)->whereUserId($this->getUser()->id)->whereVerified(1)->firstOrFail();

            $this->dispatchNow(new MakeAddressPrimaryCommand($address));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function updatePrimary(Request $request)
    {
        try {
            $address = Address::whereId($request->get('address_id'))->whereUserId($this->getUser()->id)->whereVerified(1)->firstOrFail();

            $this->dispatchNow(new MakeAddressPrimaryCommand($address));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param $userId
     * @param $addressId
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $userId, $addressId)
    {
        try {
            $address = Address::whereId($addressId)->whereUserId($userId)->wherePrimary(false)->firstOrFail();

            $this->dispatchNow(new DestroyAddressCommand($address));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }
}
