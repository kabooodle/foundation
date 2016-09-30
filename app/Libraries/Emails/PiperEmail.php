<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emails;

/**
 * Class PiperEmail
 * @package Kabooodle\Libraries\Emails
 */
class PiperEmail extends AbstractEmail
{
    /**
     * @return string
     */
    public function getEmailTemplate()
    {
        return 'emails.templates.base';
    }
}