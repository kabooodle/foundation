<?php

namespace Kabooodle\Tests\Stubs;

use Kabooodle\Models\Contracts\CommentableInterface;

/**
 * Class CommentableStub
 * @package Kabooodle\Tests\Stubs
 */
class CommentableStub implements CommentableInterface
{
    public $id = 1;

    public function getOwner()
    {
        return factory(\Kabooodle\Models\User::class)->make();
    }

    public function getName() : string
    {
        return 'shirt';
    }
}