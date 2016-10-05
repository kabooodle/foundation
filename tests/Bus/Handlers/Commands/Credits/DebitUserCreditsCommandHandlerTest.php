<?php

namespace Kabooodle\Tests\Handlers\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitFailed;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditBalanceCommand;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;
use Kabooodle\Bus\Handlers\Commands\Credits\DebitUserCreditBalanceCommandHandler;

/**
 * Class DebitUserCreditsCommandHandlerTest
 * @package Kabooodle\Tests\Handlers\Commands\Credits
 */
class DebitUserCreditsCommandHandlerTest extends BaseTestCase
{
    public function testHandler()
    {
        $stub = new DebitStub;
        $command = new DebitUserCreditBalanceCommand(
            factory(User::class)->make(),
            $stub->creditTransactionAmount(),
            $stub->getTransactionType()
        );

//        $this->setExpectedException(InsufficientBalanceException::class);
//        $this->expectsEvents([UserCreditsDebitFailed::class]);

        $handler = new DebitUserCreditBalanceCommandHandler($command);
        $handler->handle($command);
    }
}

class DebitStub implements CreditTransactableInterface
{
    public $id = 1;
    public function creditTransactionAmount()
    {
        return -10;
    }

    public function getTransactionType()
    {
        return CreditTransactableInterface::TYPE_DEBIT;
    }

    public function user()
    {
        return factory(User::class)->make();
    }
}