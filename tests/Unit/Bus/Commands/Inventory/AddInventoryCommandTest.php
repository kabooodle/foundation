<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Inventory;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Handlers\Commands\Inventory\AddInventoryCommandHandler;

/**
 * Class AddInventoryCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Inventory
 */
class AddInventoryCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'typeId' => 1,
            'styleId' => 1,
            'price' => (string) 100,
            'wholesalePrice' => (string) 100,
            'sizings' => [],
            'description' => 'foo bar'
        ];

        $object = new AddInventoryCommand(
            $params['actor'],
            $params['typeId'],
            $params['styleId'],
            $params['price'],
            $params['wholesalePrice'],
            $params['sizings'],
            $params['description']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return AddInventoryCommandHandler::class;
    }
}