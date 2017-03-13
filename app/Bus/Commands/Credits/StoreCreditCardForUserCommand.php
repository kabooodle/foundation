<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\User;

/**
 * Class StoreCreditCardForUserCommand
 * @package Kabooodle\Bus\Commands\Profile
 */
final class StoreCreditCardForUserCommand
{
    public $actor;
    public $cardNumber;
    public $expMo;
    public $expYr;
    public $cvv;
    /**
     * StoreCreditCardForUserCommand constructor.
     *
     * @param User $actor
     * @param      $cardNumber
     * @param      $expMo
     * @param      $expYr
     * @param      $cvv
     */
    public function __construct(User $actor, $cardNumber, $expMo, $expYr, $cvv)
    {
        $this->actor = $actor;
        $this->cardNumber = $cardNumber;
        $this->expMo = $expMo;
        $this->expYr = $expYr;
        $this->cvv = $cvv;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return mixed
     */
    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @return mixed
     */
    public function getExpMo()
    {
        return $this->expMo;
    }

    /**
     * @return mixed
     */
    public function getExpYr()
    {
        return $this->expYr;
    }
}
