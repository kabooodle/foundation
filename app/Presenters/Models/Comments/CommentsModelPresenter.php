<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Comments;

use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class CommentsModelPresenter
 * @package Kabooodle\Presenters\Models\Comments
 */
class CommentsModelPresenter extends PresenterAbstract
{
    public function buildComment()
    {
        $comment = $this->entity;

        return $this->view->make('comments.partials._comment')->with('_comment', $comment)->render();
    }
}
