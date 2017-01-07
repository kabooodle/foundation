<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Comments;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Tests\Stubs\CommentableStub;
use Kabooodle\Bus\Commands\Comments\AddCommentCommand;
use Kabooodle\Bus\Handlers\Commands\Comments\AddCommentCommandHandler;

/**
 * Class CreateCommentCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Comments
 */
class CreateCommentCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $stub = new CommentableStub;
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'commentable' => $stub,
            'comment' => 'test comment'
        ];

        $object = new AddCommentCommand($params['actor'], $params['commentable'], $params['comment']);

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return AddCommentCommandHandler::class;
    }
}