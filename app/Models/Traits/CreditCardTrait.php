<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

/**
 * Class CreditCardTrait
 */
trait CreditCardTrait
{
    /**
     * @return array
     */
    public function getCardRules()
    {
        return [
            'card_number' => 'required',
            'exp_month' => 'required',
            'exp_year' => 'required',
            'cvv' => 'required'
        ];
    }
}