<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Services\Queues;

use DB;
use Illuminate\Support\Str;

/**
 * Class IronQueues
 */
class IronQueues
{
    const FB_SCHEDULER_1 = 'fb_scheduler_1';
    const FB_SCHEDULER_2 = 'fb_scheduler_2';
    const FB_SCHEDULER_3 = 'fb_scheduler_3';
    const FB_SCHEDULER_4 = 'fb_scheduler_4';

    const FB_LISTER_1 = 'fb_lister_1';
    const FB_LISTER_2 = 'fb_lister_2';
    const FB_LISTER_3 = 'fb_lister_3';
    const FB_LISTER_4 = 'fb_lister_4';
    const FB_LISTER_5 = 'fb_lister_5';
    const FB_LISTER_6 = 'fb_lister_6';
    const FB_LISTER_7 = 'fb_lister_7';
    const FB_LISTER_8 = 'fb_lister_8';
    const FB_LISTER_9 = 'fb_lister_9';
    const FB_LISTER_10 = 'fb_lister_10';

    /**
     * @return array
     */
    public static function getSchedulerQueues()
    {
        return static::getConstantsStartingWith('fb_scheduler');
    }

    /**
     * @return array
     */
    public static function getListerQueues()
    {
        return static::getConstantsStartingWith('fb_lister');
    }

    /**
     * @param $startsWith
     *
     * @return array
     */
    private static function getConstantsStartingWith($startsWith)
    {
        $q = [];
        $constants = new \ReflectionClass(get_called_class());
        foreach ($constants->getConstants() as $constant) {
            if (Str::startsWith($startsWith)) {
                $q[] = $constant;
            }
        }

        return $q;
    }
}