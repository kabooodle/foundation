<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Bugsnag;
use Exception;
use Kabooodle\Models\User;
use Kabooodle\Services\Keen\KeenService;
use Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent;

/**
 * Class CreateKeenPolicyForSubscribedUser
 */
class CreateKeenPolicyForSubscribedUser
{
    /**
     * @var KeenService
     */
    public $keenService;

    /**
     * @param KeenService $keenService
     */
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

    /**
     * @param UserWasSubscribedToPlanEvent $event
     */
    public function handle(UserWasSubscribedToPlanEvent $event)
    {
        try {
            /** @var User $actor */
            $actor = $event->getActor();
            $actor->keen_access_key = $this->keenService->createScopedKeyForUser($actor);
            $actor->save();
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
