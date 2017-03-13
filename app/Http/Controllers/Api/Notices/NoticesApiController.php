<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Notices;

use Binput;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class NoticesApiController
 */
class NoticesApiController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $this->getUser();

        $notices = NotificationNotices::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate(10);
        $unread = $notices->filter(function($notice){
            return $notice->is_read == false;
        });

        return $this->setData([
            'notices' => $notices,
            'unread' => $unread
        ])->respond();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        try {
            $notices = Binput::get('ids', null);
            if ($notices) {
                NotificationNotices::whereIn('id', $notices)->where('user_id', $this->getUser()->id)->update([
                    'is_read' => true,
                    'read_at' => Carbon::now()
                ]);
            }
            return $this->noContent();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)
                ->setData([
                    'error',
                    'msg'=>'An error occurred, please try again.'
                ])
                ->respond();
        }
    }
}
