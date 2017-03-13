<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listables;

use Kabooodle\Foundation\Exceptions\Listables\ItemNotArchiveableBelongsToOutfitsException;
use Kabooodle\Models\Listable;
use Kabooodle\Bus\Commands\Listables\ArchiveListableCommand;

/**
 * Class ArchiveListableCommandHandler
 */
class ArchiveListableCommandHandler
{
    /**
     * @param ArchiveListableCommand $command
     *
     * @throws ItemNotArchiveableBelongsToOutfitsException
     */
    public function handle(ArchiveListableCommand $command)
    {
        /** @var Listable $listable */
        $listable = $command->getListable();

        if ($listable->groupings && $listable->groupings->count() > 0) {
            throw new ItemNotArchiveableBelongsToOutfitsException();
        }

        $listable->archiveModel();
    }
}
