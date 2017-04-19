<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

use Kabooodle\Models\Claims;

/**
 * Class CancelUserClaimCommand.
 */
class CancelUserClaimCommand
{
    /**
     * @var Claims
     */
    protected $claim;

    /**
     * @var string|null
     */
    protected $message;

    /**
     * CancelUserClaimCommand constructor.
     *
     * @param Claims $claim
     * @param string|null $message
     */
    public function __construct(Claims $claim, string $message = null)
    {
        $this->claim = $claim;
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getClaim(): Claims
    {
        return $this->claim;
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }
}
