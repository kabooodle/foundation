<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Listings;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Listings\GetUserListingsCommand;
use Kabooodle\Bus\Handlers\Commands\Listings\GetUserListingsCommandHandler;

/**
 * Class GetUserListingsCommandTest
 */
class GetUserListingsCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
        ];

        $object = new GetUserListingsCommand(
            $params['actor']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return GetUserListingsCommandHandler::class;
    }
}