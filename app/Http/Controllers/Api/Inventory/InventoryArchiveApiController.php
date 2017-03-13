<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Controllers\Api\Inventory;

use Binput;
use Exception;
use Illuminate\Http\Request;
use Kabooodle\Bus\Commands\Listables\ActivateListableCommand;
use Kabooodle\Models\Inventory;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Http\Controllers\Api\AbstractApiController;
use Kabooodle\Bus\Commands\Listables\ArchiveListableCommand;
use Kabooodle\Transformers\Inventory\InventoryArchiveTransformer;

/**
 * Class InventoryArchiveApiController
 */
class InventoryArchiveApiController extends AbstractApiController
{
    use DispatchesJobs;

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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkArchive(Request $request)
    {
        $ids = Binput::get('ids', []);
        $inventories = Inventory::whereIn('id', $ids)
            ->where('user_id', '=', $this->getUser()->id)
            ->with('groupings')
            ->get();
        if ($inventories->count() > 0) {
            $inventoryWithGroupings = collect([]);
            $inventoryArchived = collect([]);

            /** @var Inventory $inventory */
            foreach ($inventories as $inventory) {
                if ($inventory->groupings->count() > 0) {
                    $inventoryWithGroupings->push($inventory);
                    continue;
                }
                try {
                    $this->dispatchNow(new ArchiveListableCommand($inventory, $this->getUser()));
                    $inventoryArchived->push($inventory);
                } catch (Exception $e) {}
            }

            $msg = 'Success';
            if ($inventoryWithGroupings->count() > 0) {
                $msg = $inventoryWithGroupings->count().' of the '. $inventories->count().' could not be archived because they are associated to outfits.';
            }

            return $this->setData([
                'has_groupings' => $inventoryWithGroupings->pluck('id'),
                'archived' => $inventoryArchived->pluck('id'),
                'msg' => $msg
            ])->respond();
        }

        return $this->noContent();
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function bulkActivate(Request $request)
    {
        $ids = Binput::get('ids', []);
        $inventories = Inventory::whereIn('id', $ids)
            ->where('user_id', '=', $this->getUser()->id)
            ->with('groupings')
            ->get();
        if ($inventories->count() > 0) {
            $inventoryWithGroupings = collect([]);
            $inventoryArchived = collect([]);

            /** @var Inventory $inventory */
            foreach ($inventories as $inventory) {
                if ($inventory->groupings->count() > 0) {
                    $inventoryWithGroupings->push($inventory);
                    continue;
                }
                try {
                    $this->dispatchNow(new ActivateListableCommand($inventory, $this->getUser()));
                    $inventoryArchived->push($inventory);
                } catch (Exception $e) {}
            }

            $msg = 'Success';

            return $this->setData([
                'has_groupings' => $inventoryWithGroupings->pluck('id'),
                'activated' => $inventoryArchived->pluck('id'),
                'msg' => $msg
            ])->respond();
        }

        return $this->noContent();
    }
}
