<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class AbstractCommand.
 */
abstract class AbstractCommand
{
    use InteractsWithQueue, SerializesModels;
}
