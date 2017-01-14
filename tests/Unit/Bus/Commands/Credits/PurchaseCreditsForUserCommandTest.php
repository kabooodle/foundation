<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Profile;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Credits\PurchaseCreditsForUserCommand;
use Kabooodle\Bus\Handlers\Commands\Credits\PurchaseCreditsForUserCommandHandler;

/**
 * Class PurchaseCreditsForUserCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class PurchaseCreditsForUserCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'creditTypeId' => 1
        ];

        $object = new PurchaseCreditsForUserCommand($params['actor'], $params['creditTypeId']);

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return PurchaseCreditsForUserCommandHandler::class;
    }
}