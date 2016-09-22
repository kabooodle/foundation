<?php

namespace Kabooodle\Tests\Bus\Handlers\Commands\Profile;

use Mockery;
use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Handlers\Commands\User\AddUserCommandHandler;

/**
 * Class AddUserCommandHandlerTest
 * @package Kabooodle\Tests\Bus\Handlers\Commands\Profile
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

        $object = Mockery::mock(User::class);
        $object->shouldReceive('save')->once();

        $handler = new AddUserCommandHandler;
        $handler->handle($command);
    }
}