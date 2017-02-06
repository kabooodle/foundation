<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Kabooodle\Http\Controllers\Traits\PaginatesTrait;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Presenters\Models\Claims\ClaimsPresenter;
use Kabooodle\Models\Contracts\NotificationableInterface;

/**
 * Class Claims
 * @package Kabooodle\Models
 */
class Claims extends BaseEloquentModel implements NotificationableInterface, Revisionable
{
    use ObfuscatesIdTrait, PresentableTrait, RevisionableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'claim_status',
        'status'
    ];

    /**
     * @var array
     */
    protected $with = [
//        'shoppable',
        'shipments',
        'shipments.transaction',
        'claimer',
//        'listedItem',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'accepted_on',
        'rejected_on',
        'shipped_manually_on'
    ];

    /**
     * @var string
     */
    protected $presenter = ClaimsPresenter::class;

    /**
     * @var string
     */
    protected $table = 'claims';

    /**
     * @var array
     */
    protected $attributes = [
        'claimable_type' => null,
        'claimable_id' => 0,
        'claimed_by' => 0,
        'inventory_item_object_data' => '',
        'shoppable_id' => 0,
        'shoppable_type' => null,
        'accepted' => null,
        'accepted_price' => null,
        'accepted_on' => null,
        'rejected_on' => null,
        'rejected_by' => null,
        'rejected_reason' => null,
        'shipped_manually' => false,
        'shipped_manually_on' => null,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'claimable_type' => 'string',
        'claimable_id' => 'int',
        'claimed_by' => 'int',
        'verified' => 'bool',
        'claim_accepted' => 'bool',
        'price' => 'float',
        'inventory_item_object_data' => 'object',
        'accepted' => 'bool',
        'accepted_price' => 'float',
        'accepted_on' => 'date',
        'rejected_on' => 'date',
        'rejected_by' => 'int',
        'rejected_reason' => 'string',
        'shipped_manually' => 'bool',
        'shipped_manually_on' => 'date'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'claimable_type',
        'claimable_id',
        'claimed_by',
        'inventory_item_object_data',
        'price',
        'verified',
        'token',
        'accepted_price',
        'shoppable_id',
        'shoppable_type',
        'shipped_manually',
        'shipped_manually_on'
    ];

    /**
     * @var array
     */
    protected $hidden =[
        'shoppable_type'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($claim) {
            if (!$claim->verified) {
                $claim->token = Uuid::uuid4();
            }
        });
    }

    public function setListedItemObjectDataAttribute($value)
    {
        $this->attributes['listed_item_object_data'] = $value->toJson();
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getListedItemObjectDataAttribute($value)
    {
        return (array) json_decode($value, true);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getPriceAttribute($value)
    {
        return ! is_null($this->accepted_price) ? $this->accepted_price : $value;
    }

    public function scopeOnHold($query)
    {
        return $query->whereVerified(false)->where('created_at', '>=', Carbon::now()->sub(onHoldInterval()));
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return (bool) $this->verified;
    }

    /**
     * Verify the claim.
     *
     * @return bool
     */
    public function verify()
    {
        $this->verified = true;
        $this->token = null;
        return $this->save();
    }

    /**
     * @return bool
     */
    public function isRejected()
    {
        return (bool)$this->rejected_on;
    }

    /**
     * @return string
     */
    public function getClaimStatusAttribute()
    {
        if($this->wasAccepted()) {
            return 'Accepted';
        }

        if ($this->wasRejected()) {
            return 'Rejected';
        }

        return 'Pending';
    }

    /**
     * @return string
     */
    public function getStatusAttribute()
    {
        return $this->claim_status;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shoppable()
    {
        return $this->morphTo('shoppable')->withTrashed();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function claimable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function listedItem()
    {
        return $this->claimable();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimed_by')->with('primaryShipToAddress');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimedBy()
    {
        return $this->claimer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectedBy()
    {
        return $this->rejector();
    }

    /**
     * @return bool
     */
    public function wasRejected()
    {
        return (bool) ($this->accepted === false && $this->accepted_on == null && $this->rejected_by <> null);
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (bool) ($this->accepted === null && $this->accepted_on === null && $this->rejected_by === null);
    }

    /**
     * @return bool
     */
    public function wasAccepted()
    {
        return (bool) ($this->accepted == 1 && $this->accepted_on <> null && $this->rejected_by === null);
    }

    /**
     * @return string
     */
    public function getClaimerItemDatePriceAttribute()
    {
        $claimer = $this->claimer;
        $item = $this->listedItem;
        $date = $this->updatedAtHuman();
        $price = $this->price;

        return $item->name.', '.$claimer->name.', $'.$price.', on: '.$date;
    }

    /**
     * @return bool
     */
    public function shippedManually()
    {
        return $this->shipped_manually;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingQueue()
    {
        return $this->hasOne(ShippingQueue::class, 'claim_id');
    }

    /**
     * @return bool|mixed
     */
    public function queuedToShip()
    {
        $queue = $this->shippingQueue;
        return $queue ? $queue : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shipments()
    {
        return $this->belongsToMany(ShippingShipments::class, 'shipping_shipments_claims', 'claim_id', 'shipping_shipments_id');
    }

    /**
     * As a reminder, a claim can have many shipments, because shipments are nothing more than
     * a data entry containing prices based on the parcel data.  The user may not like this price config etc;
     * and try again.  However, you can only "transact" one claim, meaning, you can only pay for one of the
     * shipping configurations you like.  Once paid for, thats it, the item cannot be re-shipped etc;
     *
     * @return mixed
     */
    public function shipmentTransaction()
    {
        $shipments = $this->shipments;
        if ($shipments->count() > 0) {
            $shipment = $shipments->filter(function (ShippingShipments $shipment) {
                return $shipment->transaction;
            })->first();

            if ($shipment) {
                return $shipment->transaction;
            }
        }

        return false;
    }
}
