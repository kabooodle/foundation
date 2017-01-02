<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;
use Cmgmyr\Messenger\Models\Message;
use Kabooodle\Models\Traits\EmojifyableTrait;
use Kabooodle\Models\Traits\EloquentDatesTrait;
use Kabooodle\Libraries\Linkify\LinkifyableTrait;

/**
 * Class ThreadParticipants
 */
class ThreadMessages extends Message
{
    use EloquentDatesTrait, EmojifyableTrait, LinkifyableTrait;

    const CONVERT_EMOJI = true;

    /**
     * @param string $value
     */
    public function setBodyAttribute($value)
    {
        $text = $this->linkify($value);
        if (self::CONVERT_EMOJI) {
            $text = $this->emojify($text);
        }

        $this->attributes['body'] = $text;
    }
}