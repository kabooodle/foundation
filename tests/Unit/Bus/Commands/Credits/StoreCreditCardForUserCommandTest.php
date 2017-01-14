<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Credits;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Credits\StoreCreditCardForUserCommand;
use Kabooodle\Bus\Handlers\Commands\Credits\StoreCreditCardForUserCommandHandler;

/**
 * Class StoreCreditcardForUserCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class StoreCreditcardForUserCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'cardNumber' => 4242424242424242,
            'expMo' => 01,
            'expYr' => 2019,
            'cvv' => 123
        ];

        $object = new StoreCreditCardForUserCommand(
            $params['actor'],
            $params['cardNumber'],
            $params['expMo'],
            $params['expYr'],
            $params['cvv']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return StoreCreditCardForUserCommandHandler::class;
    }
}