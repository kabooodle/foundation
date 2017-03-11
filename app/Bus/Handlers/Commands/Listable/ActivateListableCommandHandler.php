<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listable;

use Kabooodle\Models\Listable;
use Kabooodle\Bus\Commands\Listable\ActivateListableCommand;

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
