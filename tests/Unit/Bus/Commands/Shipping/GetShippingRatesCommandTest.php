<?php

namespace Kabooodle\Tests\Unit\Bus\Commands\Profile;

use Kabooodle\Models\User;
use Kabooodle\Tests\BaseTestCase;
use AltThree\TestBench\CommandTrait;
use Kabooodle\Models\MailingAddress;
use Kabooodle\Services\Shippr\ParcelObject;
use Kabooodle\Bus\Commands\Shipping\GetShippingRatesCommand;
use Kabooodle\Bus\Handlers\Commands\Shipping\GetShippingRatesCommandHandler;

/**
 * Class StoreCreditcardForUserCommandTest
 * @package Kabooodle\Tests\Unit\Bus\Commands\Profile
 */
class GetShippingRatesCommandTest extends BaseTestCase
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

        $parcelObject = new ParcelObject([
            'id' => 'self',
            'length' => 3,
            'width' => 6,
            'height' => 10,
            'weight' => 20,
            'distance_unit' => 'mm',
            'weight_uom' => 'lb'
        ]);

        $params = [
            'actor' => factory(User::class)->make(),
            'claimIds' => range(0, 10),
            'recipient' => $mailingAddress,
            'sender' => $mailingAddress,
            'parcelObject' => $parcelObject
        ];

        $object = new GetShippingRatesCommand(
            $params['actor'],
            $params['claimIds'],
            $params['recipient'],
            $params['sender'],
            $params['parcelObject']
        );

        return compact('params', 'object');
    }

    /**
     * @return mixed
     */
    protected function getHandlerClass()
    {
        return GetShippingRatesCommandHandler::class;
    }
}