<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Messenger;

use Binput;
use Bugsnag;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kabooodle\Models\Threads;
use Kabooodle\Models\ThreadMessages;
use Kabooodle\Models\ThreadParticipants;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Messenger\CreateNewThreadCommand;
use Kabooodle\Bus\Commands\Messenger\SendMessengerMessageCommand;
use Kabooodle\Bus\Commands\Messenger\CreateNewMessageForThreadCommand;

/**
 * Class MessengerApiController
 */
class MessengerApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $threads = Threads::with(['messages', 'participants.user', 'participantsExcludingCreator.user'])
            ->forUser($this->getUser()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return $this->setData($threads)->respond();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'recipient' => 'required',
                'subject' => 'required|filled',
                'message' => 'required|filled'
            ]);

            $recipients = explode(',', Binput::get('recipient'));

            $this->dispatch(new CreateNewThreadCommand(
                $this->getUser(),
                $recipients,
                Binput::get('subject'),
                Binput::get('message', '')
            ));

            return $this->noContent();
        } catch (ValidationException $e) {
            return $this->setStatusCode(500)->setData([
                'msg' => $e->validator->messages()->first()
            ])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $threadId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $threadId)
    {
//        $messages = ThreadMessages::where('thread_id', $threadId)->with([
//            'user',
//            'participants' => function($query){
//                $query->whereIn('user_id', [user()->id]);
//            }])
//            ->latest('created_at')
//            ->get();
//
//        $messages = $messages->sortBy('created_at')->values()->all();
//        $messages = $this->paginateData($request, $messages, 4);

        $messages = ThreadMessages::where('thread_id', $threadId)->with([
            'user',
            'participants' => function($query){
                $query->whereIn('user_id', [$this->getUser()->id]);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->setData($messages)->respond();
    }

    /**
     * @param Request $request
     * @param         $threadId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $threadId)
    {
        try {
            $this->validate($request, ['msg' => 'required|filled']);

            $thread = Threads::ForUser($this->getUser()->id)
                ->where('messenger_threads.id', $threadId)
                ->first();

            if (! $thread) {
                throw new ModelNotFoundException;
            }

            $this->dispatch(new CreateNewMessageForThreadCommand($thread, $this->getUser(), Binput::get('msg', '')));

            return $this->noContent();
        } catch (ValidationException $e) {
            return $this->setStatusCode(401)->setData([
                'msg' => $e->validator->messages()->first()
            ])->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $threadId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function updateThreadMarkAsRead(Request $request, $threadId)
    {
        try {
            $thread = ThreadParticipants::where('thread_id', $threadId)
                ->where('user_id', $this->getUser()->id)
                ->first();

            if (! $thread) {
                throw new ModelNotFoundException;
            }

            $thread->last_read = Carbon::now();
            $thread->save();

            return $this->noContent();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     * @param         $threadId
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function destroy(Request $request, $threadId)
    {

    }
}
