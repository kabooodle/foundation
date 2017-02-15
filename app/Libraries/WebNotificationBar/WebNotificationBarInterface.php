<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries\WebNotificationBar;

/**
 * Interface WebNotificationBarInterface
 */
interface WebNotificationBarInterface
{
    /**
     * @return string
     */
    public function getContents(): string;

    /**
     * @return string
     */
    public function getType(): string;
}