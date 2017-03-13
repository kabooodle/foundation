<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Commands\User\ExtendFacebookAccessTokenCommand;

/**
 * Class ExtendFacebookAccessTokenCommandHandler
 */
class ExtendFacebookAccessTokenCommandHandler
{
    /**
     * @var FacebookSdkService
     */
    public $fb;

    /**
     * @param FacebookSdkService $facebookService
     */
    public function __construct(FacebookSdkService $facebookService)
    {
        $this->fb = $facebookService;
    }

    /**
     * @param ExtendFacebookAccessTokenCommand $command
     */
    public function handle(ExtendFacebookAccessTokenCommand $command)
    {
        $actor = $command->getActor();

        $token = $this->fb->getLongLivedAccessToken($command->getToken());

        $actor->facebook_access_token = (string) $token;
        $actor->facebook_access_token_expires = $token->getExpiresAt();
        $actor->save();
    }
}
