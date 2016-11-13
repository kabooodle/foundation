<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
     * @param         $data
     *
     * @return LengthAwarePaginator
     */
    public function paginateData(Request $request, $data)
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', config('pagination.per-page'));

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
