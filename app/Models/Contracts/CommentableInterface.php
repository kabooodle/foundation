<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
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
    public function getOwner(): User;

    /**
     * @return string
     */
    public function getName(): string;
}