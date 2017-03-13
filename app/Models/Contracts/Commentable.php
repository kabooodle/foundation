<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

use Kabooodle\Models\User;

/**
 * Interface Commentable
 * @package Kabooodle\Models\Contracts
 */
interface Commentable
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