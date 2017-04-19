<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\User;

use DB;
use Binput;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Claim\CancelUserClaimCommand;
use Kabooodle\Foundation\Exceptions\Claim\UserClaimNotCancelableException;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Models\User;
use Kabooodle\Transformers\Claims\UserClaimsTransformer;

/**
 * Class ClaimsController
 */
class ClaimsController extends AbstractApiController
{
    /**
     * @param Request $request
     * @param string  $username
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $claims = $user->claimsAsBuyer()->notCanceled()->paginate(config('pagination.per-page'));;

        return $this->response->paginator($claims, new UserClaimsTransformer);
    }

    /**
     * @param Request $request
     * @param string  $username
     * @param         $claimId
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $username, $claimId)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $claim = $user->claims->findOrFail($claimId);

        return $this->setData(['claim' => $claim])->respond();
    }

    /**
     * @param Request $request
     * @param string $username
     * @param $claimId
     *
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, string $username, $claimId)
    {
        try {
            $claim = $this->getUser()->claimsAsBuyer()->findOrFail($claimId);

            $this->dispatchNow(new CancelUserClaimCommand($claim, $request->get('message')));

            return $this->setData(['message' => 'Your claim has been canceled', 'claim' => $claim])->respond();
        } catch (UserClaimNotCancelableException $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(400)->respond($e);
        } catch (Exception $e) {
            return $this->setData(['message' => $e->getMessage()])->setStatusCode(500)->respond($e);
        }
    }
}
