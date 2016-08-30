<?php

namespace Kabooodle\Bus\Commands;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class AbstractCommand
 * @package Kabooodle\Bus\Commands
 */
abstract class AbstractCommand
{
    use InteractsWithQueue, SerializesModels;
}