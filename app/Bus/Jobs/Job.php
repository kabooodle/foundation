<?php

namespace Kabooodle\Bus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class Job
 * @package Kabooodle\Bus\Jobs
 */
abstract class Job
{
    use Queueable, SerializesModels, InteractsWithQueue;
}
