<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Contact;

use HelpScout\ApiClient;
use Illuminate\Http\Request;
use HelpScout\model\Conversation;
use HelpScout\model\thread\Customer;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Traits\ResponseTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class ContactController
 */
class ContactController extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, ['email' => 'required|email', 'name' => 'required', 'message' => 'required']);

            $hs = ApiClient::getInstance();
            $hs->setKey('5371ebd4bddc17702ca62fba43546859cefcd374');

            $customer = $hs->getCustomerRefProxy(null, $request->get('email'));

            $conversation = new Conversation();
            $conversation->setType('email');
            $conversation->setSubject('KABOOODLE Support Request');
            $conversation->setCustomer($customer);
            $conversation->setCreatedBy($customer);
            $conversation->setMailbox($hs->getMailboxProxy(83210));

            $thread = new Customer();
            $thread->setBody($request->get('message')."\n\n\n -- ".$request->get('name'));
            $thread->setCreatedBy($customer);

            $conversation->addLineItem($thread);

            $hs->createConversation($conversation);

            return $this->setData([
                'msg' => trans('alerts.contact.success')
            ])->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)->setData([
                'msg' => $e->validator->messages()->first()
            ])->respond();
        } catch (Exception $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => trans('alerts.error_generic_retry')
            ])->respond();
        }
    }
}
