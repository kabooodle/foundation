<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services\Keen;

use Kabooodle\Models\User;
use KeenIO\Client\KeenIOClient;

/**
 * Class KeenService
 */
class KeenService
{
    /**
     * @var KeenIOClient
     */
    public $keenClient;

    /**
     * @param KeenIOClient $client
     */
    public function __construct(KeenIOClient $client)
    {
        $this->keenClient = $client;
    }

    /**
     * @return KeenIOClient
     */
    public function getKeenClient()
    {
        return $this->keenClient;
    }

    /**
     * @param       $collection
     * @param array $event
     *
     * @return mixed
     */
    public function addEvent($collection, array $event = array())
    {
        return $this->getKeenClient()->addEvent($collection, $event);
    }

    /**
     * @param User $user
     *
     * @return string
     */
    public function createScopedKeyForUser(User $user)
    {
        $filter = [
            'property_name'  => 'owner.id',
            'operator'       => 'eq',
            'property_value' => $user->id,
        ];

        $filters = [$filter];
        $allowedOperations = ['read'];

        return $this->keenClient->createScopedKey($filters, $allowedOperations);
    }
}
