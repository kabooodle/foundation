<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Queue;

/**
 * Class QueuesTrait
 */
trait QueuesTrait
{
    /**
     * @param $name
     *
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getQueueConnection($name = 'iron')
    {
        $camel = ucfirst(camel_case($name));
        $method = "get{$camel}Connection";
        if (method_exists($this, $method)) {
            return $this->{$method}();
        }

        return Queue::connection($name);
    }

    /**
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getIronConnection()
    {
        return Queue::connection('iron');
    }

    /**
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getIronEmailsConnection()
    {
        return Queue::connection('iron-emails');
    }

    /**
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getIronFacebookSchedulerConnection()
    {
        return Queue::connection('iron-facebook-scheduler');
    }

    /**
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function getIronFacebookListerConnection()
    {
        return Queue::connection('iron-facebook-lister');
    }
}
