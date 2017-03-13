<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Claims;

use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Claim\VerifyClaimCommand;
use Kabooodle\Foundation\Exceptions\Claim\ClaimRejectedException;
use Kabooodle\Foundation\Exceptions\Claim\RequestedQuantityCannotBeSatisfiedException;
use Kabooodle\Http\Controllers\Web\Controller;
use Messages;

/**
 * Class ClaimsController
 */
class ClaimsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function verifyClaim(Request $request, $token)
    {
        try {
            $this->dispatchNow(new VerifyClaimCommand($token));

            return $this->redirect()->route('claims.verified');
        } catch (RequestedQuantityCannotBeSatisfiedException $e) {
            return $this->redirect()->route('claims.verification-failure');
        } catch (ClaimRejectedException $e) {
            return $this->redirect()->route('claims.verification-failure');
        } catch (Exception $e) {
            return $this->redirect()->route('claims.verification-failure');
        }
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function verifiedClaim(Request $request)
    {
        Messages::success("Claim successfully verified!");

        return $this->view('claims.verified');
    }

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function claimVerificationFail(Request $request)
    {
        Messages::error("Claim not verified!");

        return $this->view('claims.verification-failure');
    }
}
