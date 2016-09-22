<?php

namespace Kabooodle\Tests\Bus\Commands\Profile;

use Kabooodle\Tests\BaseTestCase;
use Kabooodle\Models\MailingAddress;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Bus\Commands\Profile\UpdateUserShippingAddressesCommand;
use Kabooodle\Bus\Handlers\Commands\Profile\UpdateUserShippingAddressesCommandHandler;

/**
 * Class StoreCreditcardForUserCommandTest
 * @package Kabooodle\Tests\Bus\Commands\Profile
 */
class UpdateUserShippingAddressesCommandTest extends BaseTestCase
{
    use CommandTrait;

    /**
     * @return array
     */
    protected function getObjectAndParams()
    {
        $mailingAddress = new MailingAddress(
            'Company',
            '8000 Street',
            'Suite 50',
            'Sacramento',
            'CA',
            '95610',
            'Mr. Foo Bar',
            'foo@bar.com',
            '9991234567'
        );

        $params = [
            'actor' => factory(\Kabooodle\Models\User::class)->make(),
            'fromAddress' => $mailingAddress,
            'toAddress' => $mailingAddress
        ];

        $object = new UpdateUserShippingAddressesCommand(
            $params['actor'],
            $params['fromAddress'],
            $params['toAddress']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return UpdateUserShippingAddressesCommandHandler::class;
    }
}