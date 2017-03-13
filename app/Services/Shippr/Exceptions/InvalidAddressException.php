<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Shippr\Exceptions;

use Exception;

/**
 * Class InvalidAddressException
 * @package Kabooodle\Services\Shippr\Exceptions
 */
class InvalidAddressException extends Exception
{
    protected $description;

    /**
     * InvalidAddressException constructor.
     *
     * @param string $message
     * @param null|string  $description
     */
    public function __construct($message = "", $description = null)
    {
        parent::__construct($message);

        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
}
