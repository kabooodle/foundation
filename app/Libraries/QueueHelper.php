<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries;

/**
 * Class QueueHelper
 */
class QueueHelper
{
    /**
     * @var array
     */
    public static $Q_VIEWTRACKER = [
        'iron-viewtracker',
        'iron-viewtracker-b'
    ];

    /**
     * @var array
     */
    public static $Q_FBSCHEDULER = [
        'iron-facebook-scheduler',
        'iron-facebook-scheduler-b',
        'iron-facebook-scheduler-c',
        'iron-facebook-scheduler-d',
        'iron-facebook-scheduler-e'
    ];

    /**
     * @var array
     */
    public static $Q_FBLISTER = [
        'iron-facebook-lister',
        'iron-facebook-lister-b',
        'iron-facebook-lister-c',
        'iron-facebook-lister-d',
        'iron-facebook-lister-e',
        'iron-facebook-lister-f',
        'iron-facebook-lister-g',
        'iron-facebook-lister-h',
        'iron-facebook-lister-i',
        'iron-facebook-lister-j'
    ];

    /**
     * @param array $array
     *
     * @return string
     */
    private static function makeRandomSelection(array $array)
    {
        if (in_array(app()->environment(), ['dev','local'])) {
            return 'sync';
        }

        return $array[mt_rand(0, count($array) - 1)];
    }

    /**
     * @return string
     */
    public static function pickViewTracker()
    {
        return self::makeRandomSelection(self::$Q_VIEWTRACKER);
    }

    /**
     * @return string
     */
    public static function pickFacebookScheduler()
    {
        return self::makeRandomSelection(self::$Q_FBSCHEDULER);
    }

    /**
     * @return string
     */
    public static function pickFacebookLister()
    {
        return self::makeRandomSelection(self::$Q_FBLISTER);
    }
}
