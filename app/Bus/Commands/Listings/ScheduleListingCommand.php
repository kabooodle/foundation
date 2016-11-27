<?php

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;
use Kabooodle\Models\Listings;

/**
 * Class ScheduleListingsCommand
 */
final class ScheduleListingCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var null|string
     */
    public $name;

    /**
     * @var null
     */
    public $scheduledFor;

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $facebookAlbums;

    /**
     * @var int
     */
    public $flashSaleId;

    /**
     * @var int
     */
    public $facebookGroupId;

    /**
     * @param User $actor
     * @param string|null $name
     * @param null $scheduledFor
     * @param string $type
     * @param int $flashSaleId
     * @param array $facebookAlbums
     * @param int   $facebookGroupId
     */
    public function __construct(
        User $actor,
        string $name = null,
        $scheduledFor = null,
        $type = Listings::TYPE_FACEBOOK,
        int $flashSaleId = null,
        array $facebookAlbums = [],
        int $facebookGroupId = null
    )
    {
        $this->actor = $actor;
        $this->name = $name;
        $this->scheduledFor = $scheduledFor;
        $this->type = $type;
        $this->flashSaleId = $flashSaleId;
        $this->facebookAlbums = $facebookAlbums;
        $this->facebookGroupId = $facebookGroupId;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return null
     */
    public function getScheduledFor()
    {
        return $this->scheduledFor;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getFacebookAlbums(): array
    {
        return $this->facebookAlbums;
    }

    /**
     * @return int
     */
    public function getFlashSaleId(): int
    {
        return $this->flashSaleId;
    }

    /**
     * @return int
     */
    public function getFacebookGroupId(): int
    {
        return $this->facebookGroupId;
    }
}
