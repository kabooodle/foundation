<?php

namespace Kabooodle\Tests\Bus\Handlers\Commands\Comments;

use Mockery;
use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Tests\Stubs\CommentableStub;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent;
use Kabooodle\Bus\Handlers\Commands\Comments\AddCommentCommandHandler;

/**
 * Class AddCommentCommandHandlerTest
 * @package Kabooodle\Tests\Bus\Handlers\Commands\Comments
 */
class AddCommentCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $user = factory(User::class)->create();
        $stub = new CommentableStub;

        $command = new AddCommentCommand(
            $user,
            $stub,
            'foo bar'
        );

        $this->markTestSkipped('Unable to make assertions.');

        $object = Mockery::mock(\Kabooodle\Models\Comments::class)->makePartial();
        $object->shouldReceive('save')->once()->andReturn($object);
        $this->expectsEvents([CommentWasCreatedEvent::class]);

        $this->app->instance(\Kabooodle\Models\Comments::class, $object);

        $handler = new AddCommentCommandHandler($object);
        $handler->handle($command);
    }
}