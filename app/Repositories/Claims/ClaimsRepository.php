<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Repositories\Claims;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent;
use Kabooodle\Models\Claims;
use Kabooodle\Models\User;

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
    public function getAllClaimsOnUserListables(int $userId, array $claimIds = [])
    {
        $query = "
            SELECT 
            c.id,
            u.id as user_id, 
            u.username,
            concat(u.first_name, ' ', u.last_name) as full_name,
            u.guest as is_guest,
            c.price as price,
            c.verified as verified_claim,
            c.shipped_manually_on,  
            IFNULL(c.accepted, 0) as accepted_claim,
            c.created_at as claim_created_at,
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
            IFNULL(concat(a.street1, ', ', IFNULL(a.street2, ''), a.city, ', ', a.state, ' ', a.zip), null) as shipping_address,
            ifnull(fb.facebook_node_name, ifnull(fs.name, null))  as sale_name,
            li.id as listing_id,
            ll.uuid as listing_uuid,
            group_concat(tt.tag_name SEPARATOR ',') as tag_name,
            group_concat(tt.tag_slug SEPARATOR ',') as tag_slug,
            ss.id as shipping_shipment_id,
            ss.uuid as shipping_shipment_uuid,
            st.id as shipping_transaction_id,
            st.uuid as shipping_transaction_uuid,
            if(st.id, true, false) as shipped_via_kabooodle,
            if(c.shipped_manually = 1, true, false) shipped_manually,
            st.shipping_status,
            st.shipping_status_updated_on,
            st.status,
            st.tracking_number,
            st.label_url,
            st.uuid as shipping_transaction_uuid,
            st.tracking_status,
            st.tracking_url_provider
            FROM claims as c 
            INNER JOIN listables l on l.id = c.listable_id
            INNER JOIN listing_items li on li.id = c.listing_item_id
            LEFT JOIN listings as ll ON ll.id = li.listing_id
            INNER JOIN users u on u.id = c.claimed_by
            INNER JOIN emails as e on e.user_id = u.id
            INNER JOIN files as f ON f.id = l.cover_photo_file_id
            LEFT JOIN facebook_nodes as fb on fb.facebook_node_id = ll.fb_group_node_id
            LEFT JOIN flashsales as fs on fs.id = ll.flashsale_id
            LEFT JOIN addresses as a ON a.user_id = u.id AND a.primary = 1 AND a.type = 'ship_to'    
            LEFT JOIN tagging_tagged as tt ON tt.taggable_id = c.id AND tt.taggable_type = 'Kabooodle\\\Models\\\Claims'
            LEFT JOIN (
            shipping_shipments_claims as ssc 
                INNER JOIN shipping_shipments as ss ON ss.id = ssc.shipping_shipments_id
                INNER JOIN shipping_transactions as st ON ss.id = st.shipping_shipments_id
            ) on ssc.claim_id = c.id   
            WHERE 
            l.user_id = ?
            and e.primary = 1
            and e.deleted_at is null
            and c.canceled_at is null
            and c.deleted_at is null
            and l.deleted_at is null
            ::REPLACE::
            GROUP by c.id
            ORDER BY c.created_at DESC
            ";

        $bindings = [$userId];
        if ($claimIds) {
            // We're using a RAW select with PDO bindings
            // which means we have to use a "?" for each param in the "WHERE IN ()" param. so we build this string.
            $bindingString = trim(str_repeat('?,', count($claimIds)), ',');
            $query = str_replace('::REPLACE::', ' and c.id IN ($bindingString) ', $query);
            $query = strtr($query, ['$bindingString' => $bindingString]);
            foreach ($claimIds as $claimId) {
                $bindings[] = $claimId;
            }
        } else {
            $query = str_replace('::REPLACE::', '', $query);
        }

        return DB::select($query, $bindings);
    }

    /**
     * {@inheritdoc}
     */
    public function accept(int $userId, array $claimIds)
    {
        return DB::transaction(function () use ($userId, $claimIds) {
            $timestamp = Carbon::now();

            // Accept all selected claims
            // NOTE: The only time claims are pending is when they are guest claims
            $claims = $this->model->join('listables', 'listables.id', '=', 'claims.listable_id')
                ->whereIn('claims.id', $claimIds)
                ->where('listables.user_id', '=', $userId)
                ->select('claims.*')
                ->get();

            /** @var Claims $claim */
            foreach ($claims as $claim){
                $claim->accepted_on = $timestamp;
                $claim->accepted = 1;
                $claim->save();
            }

            // Return a collection of the UPDATED models.
            return $this->getAllClaimsOnUserListables($userId, $claimIds);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function reject(int $userId, array $claimIds)
    {
        return DB::transaction(function () use ($userId, $claimIds) {
            $timestamp = Carbon::now();

            // Update
            $claims = $this->model->join('listables', 'listables.id', '=', 'claims.listable_id')
                ->whereIn('claims.id', $claimIds)
                ->where('listables.user_id', '=', $userId)
                ->select('claims.*')
                ->get();

            /** @var Claims $claim */
            foreach ($claims as $claim) {
                $claim->accepted_on = null;
                $claim->accepted_price = null;
                $claim->accepted = 0;
                $claim->rejected_on = $timestamp;
                $claim->rejected_by = $userId;
                $claim->save();

                $claim->listable->incrementInitialQty();
                $claim->delete();
            }

            // Return a collection of the UPDATED models.
            return $this->getAllClaimsOnUserListables($userId, $claimIds);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function label(int $userId, array $claimIds, array $labels)
    {
        return DB::transaction(function () use ($userId, $claimIds, $labels) {
            // Update
            $claims = $this->model->join('listables', 'listables.id', '=', 'claims.listable_id')
                ->whereIn('claims.id', $claimIds)
                ->where('listables.user_id', '=', $userId)
                ->select('claims.id')
                ->get();

            $labels = implode(',', $labels);

            /** @var Claims $claim */
            foreach ($claims as $claim) {
                $claim->tag($labels);
            }

            // Return a collection of the UPDATED models.
            return $this->getAllClaimsOnUserListables($userId, $claimIds);
        });
    }
}
