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
            'firstName' => 'jake',
            'lastName' => 'toolson',
            'username' => 'jaketoolson',
            'email' => 'jake@kabooodle.com',
            'password' => '23fasd443@$u',
            'accountType' => 'merchant'
        ];

        $object = new AddUserCommand(
            $params['firstName'],
            $params['lastName'],
            $params['username'],
            $params['email'],
            $params['password'],
            $params['accountType']
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