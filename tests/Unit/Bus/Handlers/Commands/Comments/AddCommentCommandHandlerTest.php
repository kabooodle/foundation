<?php

namespace Kabooodle\Tests\Unit\Bus\Handlers\Commands\Comments;

use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Tests\Stubs\CommentableStub;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Kabooodle\Bus\Handlers\Commands\Comments\AddCommentCommandHandler;

/**
 * Class AddCommentCommandHandlerTest
 * @package Kabooodle\Tests\Unit\Bus\Handlers\Commands\Comments
 */
class AddCommentCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $user = factory(User::class)->create();
        $stub = new CommentableStub;
        $command = new AddCommentCommand($user, $stub, 'foo bar');

        $object = factory(\Kabooodle\Models\Comments::class)->make();
        $this->app->instance(\Kabooodle\Models\Comments::class, $object);
        $handler = new AddCommentCommandHandler($object);
        $call = $handler->handle($command);
        $this->assertEquals('foo bar', $call->text);
    }
}