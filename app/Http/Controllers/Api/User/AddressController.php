<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Shippo_InvalidRequestError;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Bus\Commands\Address\AddAddressCommand;
use Kabooodle\Bus\Commands\Address\DestroyAddressCommand;
use Kabooodle\Bus\Commands\Address\MakeAddressPrimaryCommand;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\Address;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Services\Shippr\Exceptions\InvalidAddressException;
use Kabooodle\Services\Shippr\ShipprService;

/**
 * Class AddressController
 * @package Kabooodle\Http\Controllers\Api\User
 */
class AddressController extends AbstractApiController
{
    /**
     * @var ShipprService
     */
    protected $shippr;

    /**
     * AddressController constructor.
     * @param ShipprService $shippr
     */
    public function __construct(ShipprService $shippr)
    {
        $this->shippr = $shippr;
    }

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
            $validatedAddress = $this->shippr->createAndValidateAddress(new MailingAddress(
                $request->get('company'),
                $request->get('street1'),
                $request->get('street2'),
                $request->get('city'),
                $request->get('state'),
                $request->get('zip'),
                $request->get('name'),
                $this->getUser()->primaryEmail->address,
                $request->get('phone')
            ));

            $address = $this->dispatchNow(new AddAddressCommand(
                $this->getUser(),
                $validatedAddress->object_id,
                $request->get('type'),
                $request->get('primary', false),
                $validatedAddress->name,
                $validatedAddress->company,
                $validatedAddress->street1,
                $validatedAddress->street2,
                $validatedAddress->city,
                $validatedAddress->state,
                $validatedAddress->zip,
                $validatedAddress->phone,
                $validatedAddress->metadata));

            return $this->setData(['address' => $address])->respond();
        } catch (Shippo_InvalidRequestError $e) {
            return $this->setData(['msg' => 'The address fields are missing data, please try again.'])
                ->setStatusCode(401)
                ->respond($e);
        } catch (InvalidAddressException $e) {
            return $this->setData(['msg' => 'The address fields are missing data, please try again.'])->setStatusCode(401)->respond($e);
        }  catch (ValidationException $e) {
            return $this->setData(['msg' => 'The address fields are missing data, please try again.'])->setStatusCode(401)->respond($e);
        } catch (Exception $e) {
            return $this->setData(['msg' => $e->getMessage()])->setStatusCode(500)->respond($e);
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
        } catch (Shippo_InvalidRequestError $e) {
            return $this->setData(['msg' => 'The address fields are missing data, please try again.'])->setStatusCode(401)->respond($e);
        } catch (Exception $e) {
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param $userId
     *
     * @return \Illuminate\Http\Response
     */
    public function updatePrimary(Request $request, $userId)
    {
        try {
            $address = Address::whereId($request->get('address_id'))->whereUserId($userId)->whereType($request->get('type'))->firstOrFail();

            $this->dispatchNow(new MakeAddressPrimaryCommand($address));

            return $this->respond();
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
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
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
        }
    }
}
