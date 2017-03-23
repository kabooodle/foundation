<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\PhoneNumbers;

use Binput;
use Bugsnag;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\PhoneNumbers\CheckPhoneNumberVerificationCommand;
use Kabooodle\Models\PhoneNumber;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\PhoneNumbers\StartNewVerificationCommand;

/**
 * Class PhoneNumbersApiController
 */
class PhoneNumbersApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $model = PhoneNumber::where('user_id', $user->id)->latest()->first();

        return $this->setData($model)->respond();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, PhoneNumber::getRules(), ['phone_number.digits' => 'Please enter a valid 11 digit phone number, including country code.']);
            $this->dispatch(new StartNewVerificationCommand($this->getUser(), Binput::get('phone_number')));

            return $this->setData(['msg' => 'Verification code sent to ' . Binput::get('phone_number')])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->validator->messages()->first()])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $this->validate($request, PhoneNumber::getRules(), ['phone_number.digits' => 'Please enter a valid 11 digit phone number, including country code.']);

            $model = PhoneNumber::where('user_id', $this->getUser()->id)
                ->where('number', Binput::get('phone_number'))
                ->first();

            if (!$model) {
                throw new Exception('Invalid phone number and verification code. Please enter your phone number and request a new code be sent.');
            }

            $this->dispatch(new CheckPhoneNumberVerificationCommand($this->getUser(), $model, Binput::get('code')));

            return $this->setData(['msg' => 'Verification successful', 'model' => $model->fresh()->toArray()])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->setData(['msg' => $e->validator->messages()->first()])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->setData(['msg' => $e->getMessage()])->respond();
        }
    }
}