<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Flashsales;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashsaleGroups;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Validation\ValidationException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Transformers\Flashsales\FlashsaleGroups\GroupSearchTransformer;
use Kabooodle\Bus\Commands\Flashsale\FlashsaleGroups\CreateFlashsaleGroupCommand;

/**
 * Class FlashsalesGroupsApiController
 */
class FlashsalesGroupsApiController extends AbstractApiController
{
    use DispatchesJobs;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $this->validate($request, FlashsaleGroups::getRules(), FlashsaleGroups::getRuleMessages());

            $this->dispatch(new CreateFlashsaleGroupCommand(
                $this->getUser(),
                Binput::get('name'),
                Binput::get('users')
            ));

            return $this->noContent();
        } catch (ValidationException $e) {
            return $this->setData(['msg' => 'Some fields require attention', 'errors' => $e->validator->errors()])->setStatusCode(400)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setData(['msg' => 'An error occurred, please try again'])->setStatusCode(500)->respond();
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function query(Request $request)
    {
        try {
            $search = Binput::get('q');
            $exclude = Binput::get('exclude', []);
            $users = FlashsaleGroups::where('owner_id', '=', $this->user()->id)
                ->where('name', 'like', '%'.$search.'%')
                ->whereNotIn('id', $exclude)
                ->limit(10)
                ->get();

            $data = fractal()
                ->collection($users)
                ->transformWith(new GroupSearchTransformer)
                ->includeCharacters()
                ->toArray();

            return $this->setData($data)->respond();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
            return $this->setData(['data' => []])->respond();
        }
    }
}