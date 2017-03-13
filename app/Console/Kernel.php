<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Kabooodle\Console\Commands\FacebookDeletionEnqueuerCommand;
use Kabooodle\Console\Commands\FacebookEnqueuerCommand;
use Kabooodle\Console\Commands\Subscription\TrialExpiring;

/**
 * Class Kernel
 * @package Kabooodle\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * @var array
     */
    protected $commands = [
        FacebookEnqueuerCommand::class,
        FacebookDeletionEnqueuerCommand::class,
        TrialExpiring::class,
    ];

    /**
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule
//            ->command('facebook:enqueue')
//            ->everyMinute()
//            ->withoutOverlapping();
    }
}
