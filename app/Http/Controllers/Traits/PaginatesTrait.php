<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class PaginatesTrait
 * @package Kabooodle\Http\Controllers\Traits
 */
trait PaginatesTrait
{
    /**
     * @param Request $request
     * @param $data
     * @param null $perPage
     *
     * @return LengthAwarePaginator
     */
    public function paginateData(Request $request, $data, $perPage = null)
    {
        $page = $request->get('page', 1);
        $perPage = $perPage ? : $request->get('per_page', config('pagination.per-page'));

        return new LengthAwarePaginator(
            $data->forPage($page, $perPage),
            count($data),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }
}
