<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Credits\CreditUserCreditBalanceCommand;
use Stripe\Invoice;
use Kabooodle\Models\User;
use Kabooodle\Models\CreditReceipts;
use Kabooodle\Models\CreditChargeTypes;
use Kabooodle\Bus\Commands\Credits\PurchaseCreditsForUserCommand;
use Kabooodle\Bus\Events\Profile\CreditsWerePurchasedForUserEvent;

/**
 * Class PurchaseCreditsForUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Profile
 */
class PurchaseCreditsForUserCommandHandler
{
    use DispatchesJobs;

    /**
     * @param PurchaseCreditsForUserCommand $command
     *
     * @return mixed
     */
    public function handle(PurchaseCreditsForUserCommand $command)
    {
        $actor = $command->getActor();
        /** @var CreditChargeTypes $creditChargeType */
        $creditChargeType = CreditChargeTypes::findOrFail($command->getCreditTypeId());
        $cents = $this->dollarsToCents($creditChargeType->amount);

        $this->dispatchNow(new CreditUserCreditBalanceCommand($actor, $creditChargeType->amount));

        return DB::transaction(function () use ($cents, $actor, $creditChargeType) {

            // Make the invoice item request
            $response = $this->makeInvoice($cents, $actor, $creditChargeType);

            // We are returned an invoice object that contains a summary of the invoice and the sub-items.
            // However, this invoice might contain multiple line items in addition to the above
            // credit invoice item.  So lets iterate over the invoiced items and find the item we paid for from above
            // to confirm we're good and paid and can proceed with handling this.
            if ($response && $response['closed'] == true && $response['paid'] == true) {
                $items = $response['lines'];
                foreach ($items['data'] as $item) {
                    if (isset($item['metadata']['id']) && $item['metadata']['id'] == $creditChargeType->id) {
                        $receipt = new CreditReceipts;
                        $receipt->user_id = $actor->id;
                        $receipt->credit_charge_type_id = $creditChargeType->id;
                        $receipt->stripe_invoice_id = $response['id'];
                        $receipt->stripe_charge_id = $response['charge'];
                        $receipt->transaction_items = $response['lines']['data'];
                        $receipt->transaction_amount_cents = $response['total'];
                        $receipt->stripe_raw_response = $response;

                        $receipt->save();

                        event(new CreditsWerePurchasedForUserEvent);

                        return $response;
                    }
                }
            }

            return false;
        });
    }

    /**
     * @param                   $cents
     * @param User              $actor
     * @param CreditChargeTypes $creditChargeType
     *
     * @return Invoice|bool
     */
    public function makeInvoice($cents, User $actor, CreditChargeTypes $creditChargeType)
    {
        return $actor->invoiceFor($creditChargeType->name, $cents, [
            'customer' => $actor->stripe_id,
            'description' => $creditChargeType->name,
            'metadata' => $this->createMetaData($actor, $creditChargeType)
        ]);
    }

    /**
     * @param User              $user
     * @param CreditChargeTypes $chargeType
     *
     * @return array
     */
    public function createMetaData(User $user, CreditChargeTypes $chargeType)
    {
        return [
            'id' => $chargeType->id,
            'user_id' => $user->id,
            'credit_charge_id' => $chargeType->id,
        ];
    }

    /**
     * @param $dollars
     *
     * @return string
     */
    public function dollarsToCents($dollars)
    {
        return dollarsToCents($dollars, false);
    }
}
