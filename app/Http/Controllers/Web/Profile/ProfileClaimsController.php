<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Profile;

use Illuminate\Http\Request;
use Kabooodle\Models\Claims;
use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Kabooodle\Transformers\Claims\UserClaimsTransformer;

/**
 * Class ProfileClaimsController
 * @package Kabooodle\Http\Controllers\Web\Profile
 */
class ProfileClaimsController extends Controller
{
    use ObfuscatesIdTrait, PaginatesTrait;

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $claims = $this->paginateData($request, webUser()->claimsAsBuyer->sortByDesc('created_at'));

        return view('profile.claims')->with(compact('claims'));
    }

    /**
     * @param Request $request
     * @param $itemID
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function show(Request $request, $itemID)
    {
        $decryptedId = $this->obfuscateFromURIString($itemID);
        $claim = Claims::where('claimed_by', '=', webUser()->id)
            ->findOrFail($decryptedId);

        $claim->listingItem->load(['listing' => function($q){
            $q->withTrashed();
        }]);

        $data = [
            'claim' => $claim,
        ];

        return view('profile.claims.show', $data);
    }
}
