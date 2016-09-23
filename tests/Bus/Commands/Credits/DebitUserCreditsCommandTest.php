<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Tests\Bus\Commands\Credits;

use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditsCommand;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Bus\Handlers\Commands\Credits\DebitUserCreditsCommandHandler;

/**
 * Class DebitUserCreditsCommandTest
 * @package Kabooodle\Tests\Bus\Commands\Credits
 */
class DebitUserCreditsCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'transactable' => new DebitStub
        ];

        $object = new DebitUserCreditsCommand(
            $params['actor'],
            $params['transactable']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return DebitUserCreditsCommandHandler::class;
    }
}

class DebitStub implements CreditTransactableInterface
{
    public function creditTransactionAmount()
    {
        return -10;
    }
}