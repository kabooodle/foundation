<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Tests\Unit\Bus\Commands\Credits;

use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Tests\Stubs\DebitStub;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditBalanceCommand;
use Kabooodle\Bus\Handlers\Commands\Credits\DebitUserCreditBalanceCommandHandler;

/**
 * Class DebitUserCreditsCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Credits
 */
class DebitUserCreditsCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $stub = new DebitStub;
        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'debitAmount' => $stub->creditTransactionAmount(),
            'type' => $stub->getTransactionType()
        ];

        $object = new DebitUserCreditBalanceCommand(
            $params['actor'],
            $params['debitAmount'],
            $params['type']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return DebitUserCreditBalanceCommandHandler::class;
    }
}