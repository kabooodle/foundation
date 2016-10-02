<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

use Kabooodle\Models\User;

/**
 * Interface CommentableInterface
 * @package Kabooodle\Models\Contracts
 */
interface CommentableInterface
{
    /**
     * @return User
     */
    public function getOwner();

    /**
     * @return string
     */
    public function getName() : string;
}