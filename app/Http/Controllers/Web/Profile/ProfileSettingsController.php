<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Binput;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Models\ShippingAddress;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Bus\Commands\Profile\UpdateUserShippingAddressesCommand;

/**
 * Class ProfileSettingsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileSettingsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return $this->view('profile.index');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getAddresses()
    {
        $from = user()->shipFromAddress;
        $to = user()->shipToAddress;

        return $this->view('profile.addresses')->with(compact('from', 'to'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function postAddresses(Request $request)
    {
        try {
            $this->validate($request, ShippingAddress::getRules());

            $fromAddressArray = Binput::get('from');
            $toAddressArray = Binput::get('to');

            $from = new MailingAddress(
                $fromAddressArray['company'],
                $fromAddressArray['street1'],
                array_get($fromAddressArray, 'street2'),
                $fromAddressArray['city'],
                $fromAddressArray['state'],
                $fromAddressArray['zip'],
                array_get($fromAddressArray, 'email'),
                array_get($fromAddressArray, 'phone')
            );

            $to = new MailingAddress(
                array_get($toAddressArray, 'company'),
                array_get($toAddressArray, 'street1'),
                array_get($toAddressArray, 'street2'),
                array_get($toAddressArray, 'city'),
                array_get($toAddressArray, 'state'),
                array_get($toAddressArray, 'zip'),
                array_get($toAddressArray, 'email'),
                array_get($toAddressArray, 'phone')
            );

            $this->dispatchNow(new UpdateUserShippingAddressesCommand(user(), $from, $to));

            Messages::success("Mailing addresses were successfully updated!");

            return $this->redirect()->route('profile.addresses.edit');
        } catch (ValidationException $e) {
            Messages::error('Some fields require input!');

            return $this->redirect(route('profile.addresses.edit'))
                ->withErrors($e->validator->getMessageBag());
        }
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function getSocial()
    {
        return $this->view('profile.social');
    }
}
