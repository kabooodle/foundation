<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Subscriptions;

use Kabooodle\Models\User;
use Laravel\Cashier\Invoice;

/**
 * Class InvoicePaymentFailed
 */
final class InvoicePaymentFailed
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var Invoice
     */
    public $invoice;

    /**
     * @var array
     */
    public $stripePayload;

    /**
     * @param User    $user
     * @param Invoice $invoice
     * @param array   $stripePayload
     */
    public function __construct(User $user, Invoice $invoice, array $stripePayload)
    {
        $this->user = $user;
        $this->invoice = $invoice;
        $this->stripePayload = $stripePayload;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    /**
     * @return array
     */
    public function getStripePayload(): array
    {
        return $this->stripePayload;
    }
}
