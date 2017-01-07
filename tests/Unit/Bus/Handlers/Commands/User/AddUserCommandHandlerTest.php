<?php

namespace Kabooodle\Tests\Unit\Bus\Handlers\Commands\Profile;

use Mockery;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Bus\Handlers\Commands\User\AddUserCommandHandler;

/**
 * Class AddUserCommandHandlerTest
 * @package Kabooodle\Tests\Unit\Bus\Handlers\Commands\Profile
 */
class AddUserCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $command = new AddUserCommand(
            'Jake Toolson',
            'jake@kabooodle.com',
            '123456789'
        );

        $object = Mockery::mock(\Kabooodle\Models\User::class)->makePartial();
        $object->shouldReceive('factory')->once()->andReturn($object);
        $this->expectsEvents([UserWasCreatedEvent::class]);

        $this->app->instance(\Kabooodle\Models\User::class, $object);

        $handler = new AddUserCommandHandler($object);
        $handler->handle($command);
    }
}