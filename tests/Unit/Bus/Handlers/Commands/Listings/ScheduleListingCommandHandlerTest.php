<?php

namespace Kabooodle\Tests\Unit\Bus\Handlers\Commands\Listings;

use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Bus\Handlers\Commands\Listings\ScheduleListingCommandHandler;

/**
 * Class ScheduleListingCommandHandlerTest
 */
class ScheduleListingCommandHandlerTest extends BaseTestCase
{
    public function testNullScheduledForReturnsNowWithLookahead()
    {
        $handler = new ScheduleListingCommandHandler;
        $format = 'Y-m-d H:i';

        $now = Carbon::now()->addMinutes(ScheduleListingCommandHandler::EMPTY_DATE_LOOKAHEAD_MINUTES)->format($format);
        $time = $handler->normalizeScheduledDateTime(null);

        $this->assertEquals($now, $time->format($format));
    }

    public function testScheduledForReturnsMatching()
    {
        $handler = new ScheduleListingCommandHandler;
        $format = 'Y-m-d H:i:s';

        $now = '2016-01-01 09:00:00';
        $time = $handler->normalizeScheduledDateTime($now);

        $this->assertEquals($now, $time->format($format));
    }
}