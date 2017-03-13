<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Comments;

use Kabooodle\Models\Comments;
use Kabooodle\Models\Contracts\Commentable;
use Kabooodle\Models\User;

/**
 * Class UpdateCommentCommand.
 */
final class UpdateCommentCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var Commentable
     */
    public $commentable;

    /**
     * @var Comments
     */
    public $comment;

    /**
     * @var string
     */
    public $text;

    /**
     * UpdateCommentCommand constructor.
     *
     * @param User $actor
     * @param Commentable $commentable
     * @param Comments $comment
     * @param string $text
     */
    public function __construct(User $actor, Commentable $commentable, Comments $comment, string $text)
    {
        $this->actor = $actor;
        $this->commentable = $commentable;
        $this->comment = $comment;
        $this->text = $text;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return Commentable
     */
    public function getCommentable()
    {
        return $this->commentable;
    }

    /**
     * @return Comments
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
