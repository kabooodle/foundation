<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Comments;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Tests\Stubs\CommentableStub;
use Kabooodle\Bus\Commands\Comments\DeleteCommentCommand;
use Kabooodle\Bus\Handlers\Commands\Comments\DeleteCommentCommandHandler;

/**
 * Class DeleteCommentCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Comments
 */
class DeleteCommentCommandTest extends BaseTestCase
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
            'comment' => factory(\Kabooodle\Models\Comments::class)->make()
        ];

        $object = new DeleteCommentCommand($params['actor'], $params['commentable'], $params['comment']);

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return DeleteCommentCommandHandler::class;
    }
}