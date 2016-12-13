<?php

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
    public function queueEmailHandler(Request $request)
    {
        return $this->handler($request, 'iron');
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function queueFacebookListingHandler(Request $request)
    {
      return $this->handler($request, 'iron-facebook-lister');
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