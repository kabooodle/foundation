<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Listables;

use Binput;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Requests\Comments\CommentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Http\Controllers\Traits\CommentableControllerTrait;
use Kabooodle\Models\Listable;

/**
 * Class ListablesCommentsController
 */
class ListablesCommentsController extends AbstractApiController
{
    use CommentableControllerTrait;

    /**
     * @param Request $request
     * @param         $commentableId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $username, $commentableId)
    {
        $listable = Listable::findOrFail($commentableId);

        return $this->collection($listable->comments);
    }

    /**
     * @param Request $request
     * @param         $commentableId
     * @param         $commentId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $username, $commentableId, $commentId)
    {
        $listable = Listable::with('comments')->findOrFail($commentableId);

        $comment = $listable->comments->find($commentId);
        if (!$comment) {
            throw new ModelNotFoundException;
        }

        return $this->setData($comment)->respond();
    }

    /**
     * @param CommentRequest $request
     * @param                $commentableId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, $username, $commentableId)
    {
        try {
            $commentable = Listable::findOrFail(Binput::clean($commentableId));
            $data = self::handleStoreComment($this->getUser(), $commentable, $request->getCommentText(), $request->header('referer'));

            return $this->setData($data)->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->response();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->response();
        }
    }

    /**
     * @param CommentRequest $request
     * @param                $commentableId
     * @param                $commentId
     *
     * @return \Dingo\Api\Http\Response\Factory|\Illuminate\Http\Response
     */
    public function destroy(CommentRequest $request, $username, $commentableId, $commentId)
    {
        try {
            $commentable = Listable::findOrFail(Binput::clean($commentableId));
            $comment = $commentable->comments->find($commentId);
            if (!$comment) {
                throw new ModelNotFoundException;
            }
            $data = self::handleDeleteComment($this->getUser(), $commentable, $comment);

            return $this->setData($data)->respond();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->response();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->response();
        }
    }
}
