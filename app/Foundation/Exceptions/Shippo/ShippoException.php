<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

/**
 * Created by PhpStorm.
 * User: evals
 * Date: 11/7/16
 * Time: 4:21 PM
 */

namespace Kabooodle\Foundation\Exceptions\Shippo;

use Exception;

/**
 * Class ShippoException
 * @package Kabooodle\Foundation\Exceptions\Shippo
 */
class ShippoException extends Exception
{
    /**
     * ShippoException constructor.
     *
     * @param null|string $message
     * @param int              $code
     * @param Exception|null   $previous
     */
    public function __construct(string $message = null, $code = 0, Exception $previous = null)
    {
        // TODO: Identify better message handlers.
        if ($message == 'FromPhone required when service is EXPRESS.') {
            $message = 'Recipient phone number required when using EXPRESS service.';
        }
        parent::__construct($message, $code, $previous);
    }
}
