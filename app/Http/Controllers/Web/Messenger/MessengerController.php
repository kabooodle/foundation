<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Messenger;

use Illuminate\Http\Request;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Threads;

/**
 * Class MessengerController
 */
class MessengerController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        return $this->view('messenger.index');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        return $this->view('messenger.create');
    }

    /**
     * @param Request $request
     * @param         $threadId
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $threadId)
    {
        $thread = Threads::ForUser(webUser()->id)
            ->where('messenger_threads.id', $threadId)
            ->first();

        return $this->view('messenger.show')->with(compact('thread'));
    }

    public function store()
    {

    }
}