<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listable;

use Kabooodle\Models\Listable;
use Kabooodle\Bus\Commands\Listable\ArchiveListableCommand;
use Kabooodle\Foundation\Exceptions\Listings\ListingNotArchiveableBelongsToOutfitsException;

/**
 * Class ArchiveListableCommandHandler
 */
class ArchiveListableCommandHandler
{
    /**
     * @param ArchiveListableCommand $command
     *
     * @throws ListingNotArchiveableBelongsToOutfitsException
     */
    public function handle(ArchiveListableCommand $command)
    {
        /** @var Listable $listable */
        $listable = $command->getListable();

        if ($listable->groupings->count() > 0) {
            throw new ListingNotArchiveableBelongsToOutfitsException;
        }

        $listable->archiveModel();
    }
}
