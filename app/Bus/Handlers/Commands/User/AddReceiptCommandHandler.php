<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Bus\Commands\User\AddReceiptCommand;
use Kabooodle\Models\Receipt;

/**
 * Class AddReceiptCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddReceiptCommandHandler
{
    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function handle(AddReceiptCommand $command)
    {
        $receipt = $this->receipt;
        $receipt->user = $command->getUser();
        $receipt->transaction = $command->getTransaction();

        return $receipt;
    }
}
