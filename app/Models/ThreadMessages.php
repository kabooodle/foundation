<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Cmgmyr\Messenger\Models\Message;
use Kabooodle\Models\Traits\EloquentDatesTrait;

/**
 * Class ThreadParticipants
 */
class ThreadMessages extends Message
{
    use EloquentDatesTrait;
}