<?php

namespace Kabooodle\Tests\Bus\Commands\Inventory;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Inventory\AddInventoryCommand;
use Kabooodle\Bus\Handlers\Commands\Inventory\AddInventoryCommandHandler;

/**
 * Class AddInventoryCommandTest
 * @package Kabooodle\Tests\Bus\Commands\Inventory
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
            'name' => 'Test Item',
            'description' => 'Some item',
            'qty' => 1,
            'price' => 100,
        ];

        $object = new AddInventoryCommand(
            $params['actor'],
            $params['name'],
            $params['description'],
            $params['qty'],
            $params['price']
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