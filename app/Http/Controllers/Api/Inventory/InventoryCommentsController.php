<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Illuminate\Http\Request;
use Kabooodle\Models\Comments;
use Kabooodle\Models\Inventory;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Requests\Comments\CommentRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Http\Controllers\Traits\CommentableControllerTrait;

/**
 * Class InventoryCommentsController
 */
class InventoryCommentsController extends AbstractApiController
{
    use CommentableControllerTrait;

    /**
     * @param Request $request
     * @param         $commentableId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $commentableId)
    {
        $inventoryItem = Inventory::with('comments')->findOrFail($commentableId);

        return $this->collection($inventoryItem->comments);
    }

    /**
     * @param Request $request
     * @param         $commentableId
     * @param         $commentId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $commentableId, $commentId)
    {
        $comment = Comments::where('commentable_id', Binput::clean($commentableId))
            ->where('id', Binput::clean($commentId))
            ->first();

        return $this->setData($comment)->respond();
    }

    /**
     * @param CommentRequest $request
     * @param                $commentableId
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CommentRequest $request, $commentableId)
    {
        try {
            $commentable = Inventory::findOrFail(Binput::clean($commentableId));
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
    public function destroy(CommentRequest $request, $commentableId, $commentId)
    {
        try {
            $commentable = Inventory::findOrFail(Binput::clean($commentableId));
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
