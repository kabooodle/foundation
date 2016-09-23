<?php

namespace Kabooodle\Tests\Handlers\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitFailed;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditsCommand;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;
use Kabooodle\Bus\Handlers\Commands\Credits\DebitUserCreditsCommandHandler;

/**
 * Class DebitUserCreditsCommandHandlerTest
 * @package Kabooodle\Tests\Handlers\Commands\Credits
 */
class DebitUserCreditsCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $stub = new DebitStub;
        $command = new DebitUserCreditsCommand(
            factory(User::class)->make(),
            $stub
        );

        $this->setExpectedException(InsufficientBalanceException::class);
        $this->expectsEvents([UserCreditsDebitFailed::class]);

        $handler = new DebitUserCreditsCommandHandler($command);
        $handler->handle($command);
    }

    public function testInsufficientBalances()
    {
        $stub = new DebitStub;
        $command = new DebitUserCreditsCommand(
            factory(User::class)->make(),
            $stub
        );
        $handler = new DebitUserCreditsCommandHandler($command);

        $debits = [
            -00004,
            -0.00004,
            0.0005,
            0,
            1000,
            1094,
            'abc',
            '-0004',
            '99999',
            '7e9999'
        ];
        foreach ($debits as $debit) {
            $this->assertFalse($handler->hasSufficientBalance(0, $debit));
        }
    }
}

class DebitStub implements CreditTransactableInterface
{
    public $id = 1;
    public function creditTransactionAmount()
    {
        return -10;
    }
}