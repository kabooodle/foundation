<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Webhooks;

use Binput;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\BroadcastableTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class PusherWebhookController
 */
class PusherWebhookController extends Controller
{
    use BroadcastableTrait, ValidatesRequests;

    /**
     * This method ensures the private pusher channel and socket_id are valid.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function handleWebhook(Request $request)
    {
        try {
            $this->validate($request, $this->rules());

            $channelName = Binput::get('channel_name');
            $socketId = Binput::get('socket_id');

            // We are only validating private channels, which user id in the name.
            // Anything else, is treated as invalid.
            if (! Str::startsWith($channelName, ['private', 'presence'])) {
                throw new AuthorizationException;
            }

            if (Str::startsWith($channelName, 'private')) {

                // Private has a users id appended to it
                if (! Str::contains($channelName, webUser()->id)) {
                    throw new AuthorizationException;
                }
                $endpoint = 'private';
            } else {
                $endpoint = 'presence';
            }

            if ($endpoint == 'private') {
                // This returns a json string, which, we need as an array :)
                $data = json_decode($this->getPusher()->socket_auth($channelName, $socketId), true);
            } else {
                // This returns a json string, which, we need as an array :)
                $data = json_decode($this->getPusher()->presence_auth($channelName, $socketId, webUser()->id, ['username' => webUser()->username, 'full_name' => webUser()->full_name]), true);
            }

            return new JsonResponse($data);
        } catch (Exception $e) {
            return new Response('Forbidden', 403);
        }
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'channel_name' => 'required',
            'socket_id' => 'required'
        ];
    }
}
