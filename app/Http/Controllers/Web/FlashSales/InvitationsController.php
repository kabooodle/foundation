<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\FlashSales;

use Binput;
use Carbon;
use Messages;
use Illuminate\Http\Request;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Invitations;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Web\Controller;

/**
 * Class InvitationsController
 * @package Kabooodle\Http\Controllers\Web\FlashSales
 */
class InvitationsController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @param $saleIdAndName
     *
     * @return $this
     */
    public function index($saleIdAndName, $username)
    {
        // Show all inventory items in the sale
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @param $idAndName
     * @param $invitationHash
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($idAndName, $invitationHash)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = FlashSales::find($decryptedId);

        $invitation = $this->getInvitation($item, $invitationHash);

        if ($invitation) {
            return $this->view('flashsales.invitations.show')->with(compact('item', 'invitation'));
            if (webUser() && webUser()->id == $invitation->user_id) {
                // if user is logged in, present them an accept button.
                return $this->view('flashsales.invitations.show')->with(compact('item', 'invitation'));
            } else {
                // if user is not logged in, present them with the ability to login, or create an account
                return $this->view('flashsales.invitations.show')->with(compact('item', 'invitation'));
            }
        }

        return abort(404);
    }

    /**
     * @param $idAndName
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function edit($idAndName, $invitationHash)
    {
        // I am not sure we need this.
    }

    /**
     * @param Request $request
     * @param         $idAndName
     * @param         $invitationHash
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $idAndName, $invitationHash)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = FlashSales::find($decryptedId);

        $invitation = $this->getInvitation($item, $invitationHash);

        // TODO: Fix this logic
        // We need to be aware that an invitation may originally not be associated to a user
        // and therefore in the interim, if a user created an account, compare their account email against
        // the invitation email in addition to comparing the id's (if available)
        if ($invitation && webUser() && (webUser()->email == $invitation->email || webUser()->id == $invitation->user_id)) {
            $invitation->accepted = true;
            $invitation->accepted_at = \Carbon\Carbon::now();
            $invitation->save();

            $item->sellers()->save(user());

            Messages::success('You were successfully added as a seller!');
        }

        return redirect()->route('flashsales.show', [$item->getUUID()]);
    }

    /**
     * @param Request $request
     * @param         $idAndName
     * @param         $invitationHash
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $idAndName, $invitationHash)
    {
        $decryptedId = $this->obfuscateFromURIString($idAndName);
        $item = FlashSales::find($decryptedId);

        $invitation = $this->getInvitation($item, $invitationHash);

        // TODO: Fix this logic
        // We need to be aware that an invitation may originally not be associated to a user
        // and therefore in the interim, if a user created an account, compare their account email against
        // the invitation email in addition to comparing the id's (if available)
        if ($invitation && webUser() && (webUser()->email == $invitation->email || webUser()->id == $invitation->user_id)) {
            $invitation->accepted = false;
            $invitation->save();
            $invitation->delete();

            Messages::success('Invitation ignored');
        }

        return redirect()->route('flashsales.show', [$item->getUUID()]);
    }

    /**
     * @param FlashSales $flashsale
     * @param            $invitationUuid
     *
     * @return Invitations|null
     */
    private function getInvitation(FlashSales $flashsale, $invitationUuid)
    {
        return $flashsale->pendingInvitations->filter(function ($inv) use ($invitationUuid) {
            return $inv->uuid == $invitationUuid;
        })->first();
    }
}
