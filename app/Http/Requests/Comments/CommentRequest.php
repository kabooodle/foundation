<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Requests\Comments;

use Binput;
use Kabooodle\Models\Comments;
use Illuminate\Http\JsonResponse;
use Kabooodle\Http\Requests\Request;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class CommentRequest
 * @package Kabooodle\Http\Requests\Comments
 */
class CommentRequest extends Request
{
    use ObfuscatesIdTrait;

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return JsonResponse
     */
    public function forbiddenResponse()
    {
        return new JsonResponse('forbidden', 403);
    }

    /**
     * @return array
     */
    public function rules()
    {
        if ($this->isMethod('delete')) {
            return [];
        }
        return Comments::getRules();
    }

    /**
     * @return mixed
     */
    public function getCommentText()
    {
        return Binput::clean($this->request->get('text_raw'));
    }
}
