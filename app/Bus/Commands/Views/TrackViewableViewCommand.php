<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Views;

use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class TrackViewableViewCommand
 */
final class TrackViewableViewCommand
{
    use InteractsWithQueue, Queueable;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $ipAddress;

    /**
     * TrackViewableViewCommand constructor.
     *
     * @param User|null $actor
     * @param Viewable $resource
     * @param string $ipAddress
     */
    public function __construct(User $actor = null, Viewable $resource, string $ipAddress)
    {
        $this->actor = $actor;
        $this->resource = $resource;
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return User|null
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    /**
     * @return Viewable
     */
    public function getResource(): Viewable
    {
        return $this->resource;
    }
}
