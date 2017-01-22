<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Requests\Flashsale;

use Kabooodle\Models\FlashSales;
use Kabooodle\Http\Requests\Request;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class FlashsaleViewRequest
 * @package Kabooodle\Http\Requests\Flashsale
 */
class FlashsaleViewRequest extends Request
{
    use ObfuscatesIdTrait;

    /**
     * @param array $relations
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function getFlashsale($relations = [])
    {
        $idAndName = $this->route('flashsales');
        $decryptedId = $this->obfuscateFromURIString($idAndName);

        return $relations ? FlashSales::with($relations)->findOrFail($decryptedId) : FlashSales::findOrFail($decryptedId);
    }

    /**
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
