<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Listings;

use Kabooodle\Models\Listings;
use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Bus\Handlers\Commands\Listings\ScheduleListingCommandHandler;

/**
 * Class ScheduleListingCommandTest
 */
class ScheduleListingCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'name' => str_random(),
            'type' => Listings::TYPE_FACEBOOK,
            'scheduledFor' => null,
            'flashSaleId' => null,
            'facebookAlbums' => [],
            'facebookGroupId' => null
        ];

        $object = new ScheduleListingCommand(
            $params['actor'],
            $params['name'],
            $params['type'],
            $params['scheduledFor'],
            $params['flashSaleId'],
            $params['facebookAlbums'],
            $params['facebookGroupId']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return ScheduleListingCommandHandler::class;
    }
}