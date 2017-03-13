<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Notices;

use Illuminate\Http\Request;
use Kabooodle\Models\NotificationNotices;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;

/**
 * Class NoticesController
 */
class NoticesController extends Controller
{
    use PaginatesTrait;

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $notices = NotificationNotices::where('user_id', webUser()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(config('pagination.per-page'));

        return $this->view('notices.index')->with(compact('notices'));
    }
}