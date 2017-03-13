<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;

/**
 * Class KitEmail
 * @package Kabooodle\Libraries\Emails
 */
class KitEmail extends AbstractEmail
{
    /**
     * @return string
     */
    public function getEmailTemplate()
    {
        return 'emails.templates.base';
    }
}
