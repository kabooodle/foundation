<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class LogUserCreditTransactionCommand.
 */
final class LogUserCreditTransactionCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var CreditTransactableInterface
     */
    public $transactable;

    /**
     * DebitUserCreditsCommand constructor.
     *
     * @param User                        $actor
     * @param CreditTransactableInterface $transactable
     */
    public function __construct(User $actor, CreditTransactableInterface $transactable)
    {
        $this->actor = $actor;
        $this->transactable = $transactable;
    }

    /**
     * @return CreditTransactableInterface
     */
    public function getTransactable()
    {
        return $this->transactable;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }
}
