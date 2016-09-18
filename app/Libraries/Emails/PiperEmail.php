<?php

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