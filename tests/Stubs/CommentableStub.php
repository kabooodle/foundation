<?php

namespace Kabooodle\Tests\Stubs;

use Kabooodle\Models\Contracts\CommentableInterface;
use Kabooodle\Models\User;

/**
 * Class CommentableStub
 * @package Kabooodle\Tests\Stubs
 */
class CommentableStub implements CommentableInterface
{
    public $id = 1;

    public function getOwner() : User
    {
        return factory(\Kabooodle\Models\User::class)->make();
    }

    public function getName() : string
    {
        return 'shirt';
    }
}