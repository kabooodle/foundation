<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Profile;

use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;
use Kabooodle\Bus\Handlers\Commands\Social\Facebook\GetUserFacebookGroupsCommandHandler;

/**
 * Class GetUserFacebookGroupsCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class GetUserFacebookGroupsCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(User::class)->make()
        ];

        $object = new GetUserFacebookGroupsCommand($params['actor']);

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return GetUserFacebookGroupsCommandHandler::class;
    }
}