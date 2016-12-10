<?php

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