<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Exceptions\Credits;

use Exception;

/**
 * Class InsufficientBalanceException
 * @package Kabooodle\Foundation\Exceptions\Credits
 */
class InsufficientBalanceException extends Exception
{
    /**
     * InsufficientBalanceException constructor.
     *
     * @param string $message
     */
    public function __construct($message = 'Insufficient Credits Balance')
    {
        parent::__construct($message);
    }
}