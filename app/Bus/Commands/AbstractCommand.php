<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

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