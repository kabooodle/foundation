<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use JonnyPickett\EloquentSTI\SingleTableInheritance;
use Kabooodle\Models\Contracts\Claimable;
use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\Traits\ClaimableTrait;
use Kabooodle\Models\Traits\ViewableTrait;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Models\Traits\WatchableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Models\Contracts\WatchableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kabooodle\Presenters\Models\Listings\ListingItemsModelPresenter;

/**
 * Class ListingItems
 */
class ListingItems extends AbstractListingModel implements WatchableInterface, Viewable, Claimable
{
    use ObfuscatesIdTrait,
        PresentableTrait,
        SoftDeletes,
        UuidableTrait,
        ClaimableTrait,
        WatchableTrait,
        ViewableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'name',
        'id_to_string',
        'is_watched',
//        'sale_name',
    ];

    /**
     * @var string
     */
    protected $presenter = ListingItemsModelPresenter::class;

    /**
     * @var array
     */
    protected $with = [
//        'sales',
//        'watchers'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'status_updated_at',
        'scheduled_for_deletion',
        'make_available_at'
    ];

    /**
     * @var string
     */
    protected $table = 'listing_items';

    /**
     * @var array
     */
    protected $casts = [
        'ignore' => 'bool'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'uuid' => '',
        'listing_id' => null,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'fb_album_node_id' => null,
        'fb_response_object_id' => null,
        'fb_response' => '',
        'scheduled_for_deletion' => null,
        'owner_id' => 0,
        'listable_id' => 0,
        'type' => self::TYPE_FACEBOOK,
        'status' => self::STATUS_SCHEDULED,
        'status_updated_at' => '',
        'status_history' => '',
        'make_available_at' => null,
        'ignore' => false,
        'item_message' => null,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'listing_id',
        'fb_group_node_id',
        'flashsale_id',
        'fb_response_object_id',
        'fb_album_node_id',
        'fb_response',
        'owner_id',
        'listable_id',
        'type',
        'status',
        'status_updated_at',
        'status_history',
        'make_available_at',
        'ignore',
        'item_message'
    ];

    /**
     * @param $value
     *
     * @return array
     */
    public function getFbResponseAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     */
    public function setFResponseAttribute($value)
    {
        $this->attributes['fb_response'] = json_encode($value);
    }

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        return $this->listedItem->name;
    }

    /**
     * @return BelongsTo
     */
    public function facebookAlbum()
    {
        return $this->belongsTo(FacebookNodes::class, 'fb_album_node_id', 'facebook_node_id');
    }

    /**
     * @return mixed
     */
    public function claims()
    {
        return $this->hasMany(Claims::class, 'listing_item_id');
    }

    /**
     * @return mixed
     */
    public function deletedSales()
    {
        return $this->claims()->where('accepted', 0);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function flashsale()
    {
        return $this->belongsTo(FlashSales::class, 'flashsale_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listedItem(): BelongsTo
    {
        return $this->belongsTo(Listable::class, 'listable_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function listing()
    {
        return $this->belongsTo(Listings::class, 'listing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return mixed
     */
    public function pendingSales()
    {
        return $this->claims()->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function rejectedSales()
    {
        return $this->deletedSales();
    }

    /**
     * @return mixed
     */
    public function sales()
    {
        return $this->claims()->where('accepted', 1);
    }

    /**
     * @return bool
     */
    public function isIgnored(): bool
    {
        return $this->ignore;
    }

    /**
     * @return string
     */
    public function getNameOfResource(): string
    {
        if ($this->isCustomSale()) {
            return 'Custom sale';
        }

        if ($this->isFacebook()) {
            return $this->facebookAlbum->facebook_node_name;
        }

        if ($this->isFlashsale()){
            return $this->flashsale->name;
        }

        return 'Manually claimed';
    }

    /**
     * @return string
     */
    public function getNameOfShoppable(): string
    {
        return $this->getNameOfResource();
    }

    /**
     * @return mixed
     */
    public function getSaleNameAttribute()
    {
        return $this->listing ? $this->listing->sale_name : 'Manually claimed';
    }

    /**
     * @return mixed
     */
    public function getIdToStringAttribute()
    {
        return $this->obfuscateIdToString();
    }

    /**
     * @return bool
     */
    public function claimableBasedOnSchedule()
    {
        if($this->listing) {
            $now = Carbon::now();$claimableAt = $this->listing->claimable_at;
            $scheduledFor = $this->listing->scheduled_for;

            if ($scheduledFor) {
                if ($claimableAt) {
                    if ($now >= $claimableAt && $now >= $scheduledFor) {
                        return true;
                    }

                    return false;
                }

                if ($now >= $scheduledFor) {
                    return true;
                }
            }
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return mixed|null|string
     */
    public function parseItemMessage()
    {
        $itemMessage = trim($this->item_message);
        if ($itemMessage) {
            $placeholders = [
                'url' => '{url}',
                'price' => '{price}',
                'style' => '{style}',
                'size' => '{size}'
            ];

            $id = $this->obfuscateIdToString($this->id);
            $route = str_replace(['api.', 'app.', 'api', 'app'], 'www.', route('externalclaim.show', [$id]));
            $itemMessage = str_ireplace($placeholders['url'], ' '.$route.' ', $itemMessage);
            $itemMessage = str_ireplace($placeholders['price'], $this->listedItem->getPrice(), $itemMessage);

            if ($this->listedItem->subclass_name == InventoryGrouping::class) {
                $itemMessage = str_ireplace($placeholders['style'], 'Outfits', $itemMessage);
                $itemMessage = str_ireplace($placeholders['size'], null, $itemMessage);
            } else {
                $itemMessage = str_ireplace($placeholders['style'], $this->listedItem->style->name, $itemMessage);
                $itemMessage = str_ireplace($placeholders['size'], $this->listedItem->size->name, $itemMessage);
            }

            return $itemMessage;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasViewableChild(): bool
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getViewableChild()
    {
        return $this->listedItem;
    }
}
