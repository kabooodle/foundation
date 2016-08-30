<?php
namespace Kabooodle\Libraries\Messages;

use Illuminate\Support\Facades\Facade;

/**
 * Class MessagesFacade
 * @package Kabooodle\Libraries\Messages
 */
class MessagesFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'messages';
    }
}
