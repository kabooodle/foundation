<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        $camel = camel_case($name);
        $name = "get{$camel}Connection";
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }

        return Queue::connection($name);
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
