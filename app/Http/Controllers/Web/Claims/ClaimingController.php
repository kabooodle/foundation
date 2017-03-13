<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Web\Claims;

use Kabooodle\Http\Controllers\Web\Controller;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class ClaimingController
 */
class ClaimingController extends Controller
{
    use ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show($string)
    {
        return $this->redirect()->route('listingitems.show', [$string]);
    }
}
