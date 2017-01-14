<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Credits;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Credits\SubscribeUserToPlanCommand;
use Kabooodle\Bus\Handlers\Commands\Credits\SubscribeUserToPlanCommandHandler;

/**
 * Class StoreCreditcardForUserCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class SubscribeUserToPlanCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'subscriptionName' => 'foo',
            'planId' => 'bar',
            'skipTrial' => false,
            'trialDays' => 30
        ];

        $object = new SubscribeUserToPlanCommand(
            $params['actor'],
            $params['subscriptionName'],
            $params['planId'],
            $params['skipTrial'],
            $params['trialDays']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return SubscribeUserToPlanCommandHandler::class;
    }
}