<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listables;

use Kabooodle\Models\Listable;
use Kabooodle\Bus\Commands\Listables\ActivateListableCommand;

/**
 * Class ActivateListableCommandHandler
 */
class ActivateListableCommandHandler
{
    /**
     * @param ArchiveListableCommand $command
     */
    public function handle(ActivateListableCommand $command)
    {
        /** @var Listable $listable */
        $listable = $command->getListable();
        $listable->activateModel();
    }
}
