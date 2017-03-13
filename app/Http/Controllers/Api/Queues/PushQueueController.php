<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Queues;

use Illuminate\Http\Request;
use Kabooodle\Models\Traits\QueuesTrait;
use Kabooodle\Http\Controllers\Api\AbstractApiController;

/**
 * Class PushQueueController
 */
class PushQueueController extends AbstractApiController
{
    use QueuesTrait;

    /**
     * @param Request $request
     * @param string  $queueName
     *
     * @return mixed
     */
    protected function handler(Request $request, $queueName = 'iron')
    {
        $queue = $this->getQueueConnection($queueName);

        return $queue->marshal();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function queueGeneralHandler(Request $request)
    {
        return $this->handler($request, 'iron');
    }





    public function queueEmailHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails');
    }
    public function queueEmailBHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-b');
    }
    public function queueEmailCHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-c');
    }
    public function queueEmailDHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-d');
    }
    public function queueEmailEHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-e');
    }
    public function queueEmailFHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-f');
    }
    public function queueEmailGHandler(Request $request)
    {
        return $this->handler($request, 'iron-emails-g');
    }






    public function queueViewTrackerHandler(Request $request)
    {
        return $this->handler($request, 'iron-viewtracker');
    }
    public function queueViewTrackerBHandler(Request $request)
    {
        return $this->handler($request, 'iron-viewtracker-b');
    }




    

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function queueFacebookScheduleHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-scheduler');
    }
    public function queueFacebookScheduleBHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-scheduler-b');
    }
    public function queueFacebookScheduleCHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-scheduler-c');
    }
    public function queueFacebookScheduleDHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-scheduler-d');
    }
    public function queueFacebookScheduleEHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-scheduler-e');
    }






    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function queueFacebookListingHandler(Request $request)
    {
      return $this->handler($request, 'iron-facebook-lister');
    }
    public function queueFacebookListingBHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-b');
    }
    public function queueFacebookListingCHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-c');
    }
    public function queueFacebookListingDHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-d');
    }
    public function queueFacebookListingEHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-e');
    }
    public function queueFacebookListingFHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-f');
    }
    public function queueFacebookListingGHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-g');
    }
    public function queueFacebookListingHHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-h');
    }
    public function queueFacebookListingIHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-i');
    }
    public function queueFacebookListingJHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-lister-j');
    }






    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function queueFacebookDeleterHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-deleter');
    }
    public function queueFacebookDeleterBHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-deleter-b');
    }
    public function queueFacebookDeleterCHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-deleter-c');
    }
    public function queueFacebookDeleterDHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-deleter-d');
    }
    public function queueFacebookDeleterEHandler(Request $request)
    {
        return $this->handler($request, 'iron-facebook-deleter-e');
    }

    /**
     * @param Request $request
     *
     * @return mixedGREE
     */
    public function errorQueueHandler(Request $request)
    {
        return $this->handler($request);
    }
}
