<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use Binput;
use Exception;
use Kabooodle\Models\User;
use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Transformers\UserSearchTransformer;

/**
 * Class QueryUser
 */
class QueryUser extends AbstractApiController
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function query(Request $request)
    {
        try {
            $search = Binput::get('q');
            $users = User::where('id', '<>', $this->user()->id)
                ->where(function($query) use ($search) {
                    $query->where('username', 'like', '%'.$search.'%');
                    $query->orWhere('first_name', 'like', '%'.$search.'%');
                    $query->orWhere('last_name', 'like', '%'.$search.'%');
                })
                ->limit(10)
                ->get();

            $data = fractal()
                ->collection($users)
                ->transformWith(new UserSearchTransformer)
                ->includeCharacters()
                ->toArray();

            return $this->setData($data)->respond();
        } catch (Exception $e) {
            return $this->setData(['data' => []])->respond();
        }
    }
}