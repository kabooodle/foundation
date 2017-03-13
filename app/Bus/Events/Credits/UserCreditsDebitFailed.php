<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Credits;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class UserCreditsDebitedEvent
 * @package Kabooodle\Bus\Events\Credits
 */
final class UserCreditsDebitFailed extends Event implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var CreditTransactableInterface
     */
    public $transactable;

    /**
     * UserCreditsDebitFailed constructor.
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
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return CreditTransactableInterface
     */
    public function getTransactable()
    {
        return $this->transactable;
    }
}
