<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Shipping;

use Bugsnag;
use Exception;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Services\Keen\KeenService;
use Kabooodle\Models\ShippingTransactions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent;

/**
 * Class SendShippingDataToKeen
 */
class SendShippingDataToKeen implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var KeenService
     */
    public $keenService;

    /**
     * @param KeenService $keenService
     */
    public function __construct(KeenService $keenService)
    {
        $this->keenService = $keenService;
    }

    /**
     * @param ShippingTransactionWasCreatedEvent $command
     */
    public function handle(ShippingTransactionWasCreatedEvent $command)
    {
        /** @var ShippingTransactions $shippingTransaction */
        $shippingTransaction = $command->getShippingTransaction();

        try {
            $data = [
                'id' => $shippingTransaction->id,
                'uuid' => $shippingTransaction->uuid,
                'adjusted_cost' => $shippingTransaction->rate_amount,
                'label_rate' => $shippingTransaction->rate_final_amount,
                'raw_object' => $shippingTransaction,
                'created_at' => $shippingTransaction->created_at,
                'updated_at' => $shippingTransaction->updated_at,

                // Objects
                'shipment' => $shippingTransaction->shipment,
                'claims' => $shippingTransaction->shipment->claims,
                'owner' => $shippingTransaction->user, // User model
                'recipient' => $shippingTransaction->recipient, // User model

                // Keen meta
                "keen" => [
                    "addons" => [
                        [
                            "name" => "keen:date_time_parser",
                            "input" => [ "date_time" => "updated_at.date" ],
                            "output" => "updated_at_timestamp"
                        ]
                    ]
                ],
            ];

            $this->keenService->keenClient->addEvent('shipping_transactions', $data);
        } catch (Exception $e) {
            Bugsnag::notifyException($e);
        }
    }
}
