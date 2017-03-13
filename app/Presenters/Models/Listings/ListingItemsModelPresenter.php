<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Listings;

use Kabooodle\Models\ListingItems;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class ListingItemsModelPresenter
 */
class ListingItemsModelPresenter extends PresenterAbstract
{
    use ListingsPresenterTrait;

    /**
     * @return null|string
     */
    public function facebookPhotoLink()
    {
        $entity = $this->entity;
        if ($entity->fb_response_object_id && $entity->fb_album_node_id ) {
            // Even if the image was deleted from facebook already, leave the link as a quick way to confirm.
            // if ($entity->status == ListingItems::STATUS_SUCCES) {
                return '<a style="color: inherit" target="_blank" href="https://www.facebook.com/photo.php?fbid='.$entity->fb_response_object_id.'&set=oa.'.$entity->fb_album_node_id.'&type=3&theater"><i class="fa fa-external-link" aria-hidden="true"></i></a>';
            // }
        }

        return null;
    }
}
