<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Repositories\Claims;

use DB;
use Kabooodle\Models\Claims;

/**
 * Class ClaimsRepository
 */
class ClaimsRepository implements ClaimsRepositoryInterface
{
    /**
     * @var Claims
     */
    public $model;

    /**
     * @param Claims $model
     */
    public function __construct(Claims $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllClaimsOnUserListables(int $userId)
    {
        $query = "
            SELECT 
            u.id, 
            u.username,
            concat(u.first_name, ' ', u.last_name) as full_name,
            u.guest as is_guest,
            c.price as price,
            c.verified as verified_claim,
            c.accepted as accepted_claim,
            c.accepted_on,
            c.rejected_on,
            c.shipped_manually,
            c.shipped_manually_on,
            c.created_at as claim_created_at,
            c.uuid as claim_uuid,
            c.listable_id as listable_id,
            c.listable_type as listable_type,
            l.slug as slug,
            l.subclass_name as subclass,
            l.listable_type_id,
            l.name_alt,
            l.price_usd as listable_price,
            f.location as listable_cover_photo_location,
            f.key as  listable_cover_photo_key,
            e.address as email,
            concat(a.street1, ', ', IFNULL(a.street2, ''), a.city, ', ', a.state, ' ', a.zip) as shipping_address,
            ifnull(fb.facebook_node_name, fs.name)  as sale_name,
            ll.id as listing_id
            FROM claims as c 
            INNER JOIN listables l on l.id = c.listable_id
            INNER JOIN listing_items li on li.id = c.listing_item_id
            INNER JOIN listings as ll ON ll.id = li.listing_id
            INNER JOIN users u on u.id = l.user_id 
            INNER JOIN emails as e on e.user_id = u.id
            INNER JOIN files as f ON f.id = l.cover_photo_file_id
            LEFT JOIN facebook_nodes as fb on fb.facebook_node_id = ll.fb_group_node_id
            LEFT JOIN flashsales as fs on fs.id = ll.flashsale_id
            LEFT JOIN addresses as a ON a.user_id = u.id AND a.primary = 1 AND a.type = 'ship_to'    
            WHERE 
            l.user_id = ?
            and e.primary = 1
            and e.deleted_at is null
            and c.deleted_at is null
            and l.deleted_at is null
            ";

        return DB::select($query, [$userId]);
    }
}
