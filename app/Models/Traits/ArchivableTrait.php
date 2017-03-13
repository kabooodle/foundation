<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArchivableTrait
 */
trait ArchivableTrait
{
    public function getStateArchivedAtColumn()
    {
        return 'archived_at';
    }

    /**
     * @param Builder   $query
     * @param bool      $isArchived
     *
     * @return mixed
     */
    public function scopeActive($query, $isArchived = true)
    {
        return $query->archived(!$isArchived);
    }

    /**
     * @param Builder   $query
     * @param bool      $isArchived
     *
     * @return mixed
     */
    public function scopeArchived($query, $isArchived = true)
    {
        $query = $query->whereNull('deleted_at');

        return $isArchived === true ? $query->whereNotNull('archived_at') : $query->whereNull('archived_at');
    }

    /**
     * Activate this model.
     *
     * @return Builder
     */
    public function activateModel()
    {
        $this->archived_at = null;
        $this->deleted_at = null;
        $this->save();
    }

    /**
     * Archive this model.
     *
     * @return Builder
     */
    public function archiveModel()
    {
        $this->archived_at = Carbon::now();
        $this->deleted_at = null;
        $this->save();
    }
}
