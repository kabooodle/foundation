<?php


namespace Kabooodle\Tests\Bus\Handlers\Commands\Listings;

use Kabooodle\Models\Listings;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Bus\Handlers\Commands\Listings\ScheduleListingCommandHandler;

class ScheduleListingCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $user = factory(User::class)->create();
        $stub = new CommentableStub;
        $command = new ScheduleListingCommand(
            $user,
            str_random(),
            Listings::TYPE_FACEBOOK,
            null,
            null,
            // albums
            // groupid
        );
    }
}