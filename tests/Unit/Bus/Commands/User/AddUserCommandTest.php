<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Profile;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Handlers\Commands\User\AddUserCommandHandler;

/**
 * Class StoreCreditcardForUserCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class AddUserCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'name' => 'Jake T',
            'email' => 'jake@kabooodle.com',
            'password' => '23fasd443@$u'
        ];

        $object = new AddUserCommand(
            $params['name'],
            $params['email'],
            $params['password']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return AddUserCommandHandler::class;
    }
}