<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Kabooodle\Models\Inventory;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Transformers\Inventory\InventoryArchiveTransformer;

/**
 * Class InventoryArchiveApiController
 */
class InventoryArchiveApiController extends AbstractApiController
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Inventory::noEagerLoads()->archived()
            ->with(['claims', 'style', 'styleSize', 'files'])
            ->where('user_id', '=', $this->getUser()->id)
            ->paginate(config('pagination.per-page'));

        return $this->response->paginator($data, new InventoryArchiveTransformer);
    }
}
