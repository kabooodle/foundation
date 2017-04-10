<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Sofa\Revisionable\Revisionable;
use Illuminate\Auth\Authenticatable;
use Kabooodle\Models\Traits\Messagable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Kabooodle\Bus\NotificationableTrait;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;
use Kabooodle\Presenters\Models\UserModelPresenter;
use Illuminate\Foundation\Auth\Access\Authorizable;
use SammyK\LaravelFacebookSdk\SyncableGraphNodeTrait;
use Kabooodle\Bus\Commands\Social\Facebook\GetUserFacebookGroupsCommand;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

/**
 * Class User
 * @package Kabooodle
 */
class User extends BaseEloquentModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract,
    JWTSubject,
    LikeableInterface,
    Revisionable
{
    use Authenticatable,
        Authorizable,
        Billable,
        CanResetPassword,
        DispatchesJobs,
        FollowableTrait,
        LikeableTrait,
        Messagable,
        NotificationableTrait,
        ObfuscatesIdTrait,
        PresentableTrait,
        RevisionableTrait,
        SyncableGraphNodeTrait;

    /**
     * @var array
     */
    protected $appends = [
        'is_following',
        'full_name',
        'full_name_with_username',
        'name',
        'email',
        'profile_url',
        'user_avatar'
    ];

    /**
     * @var array
     */
    protected $with = [
//        'creditBalance'
    ];

    /**
     * @var string
     */
    protected $presenter = UserModelPresenter::class;

    /**
     * @var array
     */
    protected $dates =[
        'trial_ends_at',
        'facebook_access_token_expires'
    ];

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getAlgoliaRecord()
    {
        return array_merge($this->toArray(), [
            'oid' => $this->getUUID(),
        ]);
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'exp' =>
                Carbon::now()->addMinutes(config('jwt.ttl'))
        ];
    }

    /**
     * @var array
     */
    protected $casts = [
        'activated' => 'boolean',
        'guest' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'password',
        'invited_by_user_id',
        'trial_ends_at',
        'activated',
        'guest',
        'timezone',
        'kabooodle_as_shipping',
        'referred_by_user_id',
        'keen_access_key'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'kabooodle_as_shipping',
        'facebook_access_token_expires',
        'stripe_id',
        'creditBalance',
        'card_brand',
        'credit_balance',
        'referred_by_user_id',
        'card_last_four',
        'trial_ends_at',
        'pivot',
        'activated',
        'access_token',
        'facebook_access_token',
        'facebook_user_id',
        'superuser'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'account_type' => 'required|in:basic,merchant,merchant_plus',
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required|unique:users|min:5|max:30',
            'email' => 'required|email|max:255|unique:emails,address',
            'password' => 'required|min:6',
        ];
    }

    /**
     * @param User $guest
     *
     * @return array
     */
    public static function getConvertGuestRules(User $guest)
    {
        return [
            'username' => 'required|min:5|max:30|unique:users,username,'.$guest->id,
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ];
    }

    /**
     * @return array
     */
    public static function getGuestRules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|max:255',
            'street1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'phone' => 'required',
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            if (!$user->username) {
                $user->username = self::_createUsername($user->first_name.$user->last_name);
            }
            $user->public_hash = self::_createHash();
        });

        self::saving(function ($user) {
            $user->first_name = trim($user->first_name);
            $user->last_name = trim($user->last_name);
        });
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    public static function factory(array $attributes)
    {
        return self::create($attributes);
    }

    /**
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return string
     */
    public function getFullNameWithUsernameAttribute()
    {
        return $this->first_name.' '.$this->last_name.' ('.$this->username.')';
    }

    /**
     * @return mixed
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    /**
     * @return string|null
     */
    public function getEmailAttribute()
    {
        return $this->primaryEmail ? $this->primaryEmail->address : null;
    }

    public function getUserAvatarAttribute()
    {
        return $this->avatar;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function avatar()
    {
        return $this->morphOne(Files::class, 'fileable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany(Email::class, 'user_id')->orderBy('primary', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryEmail()
    {
        return $this->hasOne(Email::class, 'user_id')->wherePrimary(1);
    }

    /**
     * @param Email $primaryEmail
     */
    public function makeEmailOnlyPrimary(Email $primaryEmail)
    {
        $otherEmails = $this->emails->filter(function ($email) use ($primaryEmail) {
            return $email->id != $primaryEmail->id;
        });
        foreach ($otherEmails as $otherEmail) {
            $otherEmail->primary = false;
            $otherEmail->save();
        }

        if (!$primaryEmail->isPrimary()) {
            $primaryEmail->primary = true;
            $primaryEmail->save();
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    /**
     * @return bool
     */
    public function accountActivated()
    {
        return $this->activated;
    }

    /**
     * @return bool
     */
    public function isGuest()
    {
        return (bool) $this->guest;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listables()
    {
        return $this->hasMany(Listable::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryGroupings()
    {
        return $this->hasMany(InventoryGrouping::class, 'user_id');
    }

    /**
     * @return array
     */
    public function inventoryGroupByStyle()
    {

//        $sql = "SELECT inventory_type_styles_id,countstlyes as count_styles, inventory_sizes_id, count(inventory_sizes_id) sizes, st.name, sz.name FROM (
//                SELECT inventory_type_styles_id, count(*) as countstlyes, inventory_sizes_id, user_id
//                FROM `inventory`
//                WHERE user_id = {$this->id}
//                GROUP BY inventory_type_styles_id, inventory_sizes_id
//                )as t
//                INNER JOIN `inventory_type_styles` as st ON st.id = inventory_type_styles_id
//                INNER JOIN `inventory_sizes` as sz ON sz.id = inventory_sizes_id
//              GROUP BY inventory_type_styles_id, inventory_sizes_id";

        $sql = "SELECT st.name as name, inventory_type_styles_id, count(inventory_type_styles_id) as styles_count FROM inventory
		INNER JOIN `inventory_type_styles` as st ON st.id = inventory_type_styles_id
		WHERE user_id = {$this->id}
	GROUP BY inventory_type_styles_id";

        return DB::select($sql);
    }

    /**
     * @return mixed
     */
    public function getTZ()
    {
        return $this->timezone;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flashsales()
    {
        return $this->hasMany(FlashSales::class, 'user_id')->orderBy('starts_at');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingsInFlashsales()
    {
        return $this->listings()->where('type', Listings::TYPE_FLASHSALE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingsOnFacebook()
    {
        return $this->listings()->where('type', Listings::TYPE_FACEBOOK);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listings()
    {
        return $this->hasMany(Listings::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItemsInFlashsales()
    {
        return $this->listingItems()->where('type', Listings::TYPE_FLASHSALE);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItemsInFacebook()
    {
        return $this->listingItems()->where('type', Listings::TYPE_FACEBOOK);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItems()
    {
        return $this->hasMany(ListingItems::class, 'owner_id');
    }

    /**
     * @return mixed
     */
    public function flashsalesAsSeller()
    {
        return FlashSales::join('flashsales_sellers_groups', 'flashsales_sellers_groups.flashsale_id', '=', 'flashsales.id')
            ->join('flashsales_groups_users', 'flashsales_groups_users.flashsales_group_id', '=', 'flashsales_sellers_groups.flashsale_group_id')
            ->join('users', 'flashsales_groups_users.user_id', '=', 'users.id')
            ->groupBy('flashsales.id')
            ->where('users.id', '=', $this->id)
            ->whereNull('flashsales.deleted_at');
    }

    /**
     * @return mixed
     */
    public function flashsalesAsAdmin()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsales_admins', 'user_id', 'flashsale_id')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function flashsalesAsOwner()
    {
        return $this->hasMany(FlashSales::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function currentFlashsales()
    {
        return $this->flashsales()->where('starts_at', '>=', 'NOW()')->where('ends_at' , '>=', 'NOW()');
    }

    /**
     * @return mixed
     */
    public function currentFlashsalesAsOwner()
    {
        return $this->flashsalesAsOwner()->where('starts_at', '>=', 'NOW()')
            ->where('ends_at' , '>=', 'NOW()');
    }

    /**
     * @return mixed
     */
    public function currentFlashsalesAsAdmin()
    {
        return $this->flashsalesAsAdmin()->where('starts_at', '>=', 'NOW()')->where('ends_at' , '>=', 'NOW()');
    }

    /**
     * @return mixed
     */
    public function currentFlashsalesAsSeller()
    {
        return $this->flashsalesAsSeller()->where('flashsales.starts_at', '>=', 'NOW()')->where('ends_at' , '>=', 'NOW()');
    }

    /**
     * @return $this|\Illuminate\Support\Collection
     */
    public function currentFlashsalesAsSellerAndAdmins()
    {
        // Start with empty collection
        $flashsales = collect();

        $asOwner = $this->currentFlashsalesAsOwner;
        if ($asOwner) {
            foreach ($asOwner as $ownerFlashsale) {
                $ownerFlashsale->my_post_time = null;
                $flashsales->push($ownerFlashsale);
            }
        }

        $asAdmin = $this->currentFlashsalesAsAdmin;
        if ($asAdmin){
            foreach ($asAdmin as $adminFlashsale) {
                $ownerFlashsale->my_post_time = null;
                $flashsales->push($adminFlashsale);
            }
        }

        $asSeller = $this->currentFlashsalesAsSeller()
            ->get([
                'flashsales.id',
                'flashsales.name',
                'flashsales.starts_at',
                'flashsales.ends_at',
                'flashsales.privacy',
                'flashsales_sellers_groups.time_slot'
            ]);
        if ($asSeller->count() > 0) {
            foreach ($asSeller as $asSellerFlashsale) {
                $ownerFlashsale->my_post_time = $asSellerFlashsale->time_slot;
                $flashsales = $flashsales->push($asSellerFlashsale);
            }
        }

        return $flashsales->unique('id')->sortBy('starts_at')->values()->all();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function flashsalesInvitations()
    {
        return $this->morphedByMany(FlashSales::class, 'invitable', 'invitations', 'user_id', 'invitable_id')->withPivot(['uuid', 'invited_at', 'invited_by', 'user_id', 'email', 'accepted', 'accepted_at']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groupsInvitations()
    {
        return $this->morphedByMany(Groups::class, 'invitable', 'invitations', 'user_id', 'invitable_id')->withPivot(['uuid', 'invited_at', 'invited_by', 'user_id', 'email', 'accepted', 'accepted_at']);
    }

    /**
     * @return mixed
     */
    public function allMyInvitations()
    {
        return $this->flashsalesInvitations->merge($this->groupsInvitations);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupsAsMember()
    {
        return $this->belongsToMany(Groups::class, 'groups_members', 'user_id', 'group_id')->orderBy('name');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupsAsAdmin()
    {
        return $this->belongsToMany(Groups::class, 'groups_admins', 'user_id', 'group_id')->orderBy('name');
    }

    /**
     * @return Collection
     */
    public function allMyGroups()
    {
        return $this->groupsAsMember->merge($this->groupsAsAdmin);
    }

    /**
     * @return mixed
     */
    public function likedFlashsales()
    {
        return $this->morphedByMany(FlashSales::class, Likes::LIKEABLE_COL)->whereDeletedAt(null);
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id')->orderBy('primary', 'desc');
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryBillingAddress()
    {
        return $this->hasOne(Address::class, 'user_id')->whereType(Address::TYPE_BILLING)->wherePrimary(1);
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function billingAddresses()
    {
        return $this->hasMany(Address::class, 'user_id')->whereType(Address::TYPE_BILLING)->orderBy('primary', 'desc');
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryShipFromAddress()
    {
        return $this->hasOne(Address::class, 'user_id')->whereType(Address::TYPE_FROM)->wherePrimary(1);
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shipFromAddresses()
    {
        return $this->hasMany(Address::class, 'user_id')->whereType(Address::TYPE_FROM)->orderBy('primary', 'desc');
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryShipToAddress()
    {
        return $this->hasOne(Address::class, 'user_id')->whereType(Address::TYPE_TO)->wherePrimary(1);
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shipToAddresses()
    {
        return $this->hasMany(Address::class, 'user_id')->whereType(Address::TYPE_TO)->orderBy('primary', 'desc');
    }

    /**
     * @param Address $primaryAddress
     */
    public function makeAddressOnlyPrimary(Address $primaryAddress)
    {
        $otherAddresses = $this->addresses->filter(function ($address) use ($primaryAddress) {
            return $address->type == $primaryAddress->type && $address->id != $primaryAddress->id;
        });
        foreach ($otherAddresses as $otherAddress) {
            $otherAddress->primary = false;
            $otherAddress->save();
        }

        if (!$primaryAddress->isPrimary()) {
            $primaryAddress->primary = true;
            $primaryAddress->save();
        }
    }

    /**
     * @param $name
     *
     * @return string
     */
    private static function _createUsername($name)
    {
        $username = Str::slug(str_replace(' ', '', $name));
        if (self::where('username', $username)->count() >= 1) {
            return self::_createUsername($name . Str::quickRandom(2));
        }

        return $username;
    }

    /**
     * @return string
     */
    public function getNameOfResource(): string
    {
        return 'Merchant';
    }

    /**
     * @return mixed
     */
    private static function _createHash()
    {
        $hash = Str::random(9);
        if (self::where('public_hash', $hash)->count() >= 1) {
            return self::_createHash();
        }

        return $hash;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsOnMyListables()
    {
        return $this->hasManyThrough(Claims::class,  Listable::class, 'user_id', 'listable_id', 'id')
            ->where('listables.user_id', $this->id)
            ->with(['shipments', 'shipments.transaction']);
    }

    /**
     * @return mixed
     */
    public function acceptedClaimsOnMyListables()
    {
        return $this->claimsOnMyListables()
            ->whereAccepted(true)
            ->whereNotNull('accepted_on')
            ->orderBy('accepted_on', 'desc');
    }

    /**
     * @return mixed
     */
    public function pendingClaimsOnMyListables()
    {
        return $this->claimsOnMyListables()
            ->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function acceptedClaimsOnMyInventory()
    {
        return $this->claimsOnMyInventory()
            ->whereAccepted(true)
            ->whereNotNull('accepted_on')
            ->orderBy('accepted_on', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsOnMyInventory()
    {
        return $this->hasManyThrough(Claims::class,  Inventory::class, 'user_id', 'listable_id', 'id')
            ->where('claims.listable_type', Inventory::class)
            ->where('listables.user_id', $this->id)
            ->with(['shipments', 'shipments.transaction']);
    }

    /**
     * @return mixed
     */
    public function pendingClaimsOnMyInventory()
    {
        return $this->claimsOnMyInventory()
            ->whereNull('accepted');
    }

    /**
     * @return mixed
     */
    public function acceptedClaimsOnMyInventoryGroupings()
    {
        return $this->claimsOnMyInventoryGroupings()
            ->whereAccepted(true)
            ->whereNotNull('accepted_on')
            ->orderBy('accepted_on', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsOnMyInventoryGroupings()
    {
        return $this->hasManyThrough(Claims::class,  InventoryGrouping::class, 'user_id', 'listable_id', 'id')
            ->where('claims.listable_type', InventoryGrouping::class)
            ->where('listables.user_id', $this->id)
            ->with(['shipments', 'shipments.transaction']);
    }

    /**
     * @return mixed
     */
    public function pendingClaimsOnMyInventoryGroupings()
    {
        return $this->claimsOnMyInventoryGroupings()
            ->whereNull('accepted');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsAsSeller()
    {
        return $this->claimsOnMyListables();
    }

    /**
     * TODO: This is not optimized. Optimize it.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsAsSellerNoShipping()
    {
        $claims = $this->claimsOnMyListables;
        if ($claims->count() > 0) {
            return $claims->filter(function (Claims $claim) {
                // Ignore claims still pending and ones that have already shipped
                return ($claim->wasAccepted() && ! $claim->shipmentTransaction() ? true : false);
            })->values();
        }

        return $claims;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingTransactions()
    {
        return $this->hasMany(ShippingTransactions::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function unfulfilledShippingTransactions()
    {
        return $this->shippingTransactions()->where('fulfilled', '=', 0);
    }

    /**
     * @return mixed
     */
    public function fulfilledShippingTransactions()
    {
        return $this->shippingTransactions()->where('fulfilled', '=', 1);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function shippingQueue()
    {
        return $this->belongsToMany(Claims::class, ShippingQueue::getTableName(), 'user_id', 'claim_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function queuedToShip()
    {
        return $this->shippingQueue();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsAsBuyer()
    {
        return $this->hasMany(Claims::class, 'claimed_by')->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function facebookGroups()
    {
        return $this->belongsToMany(FacebookNodes::class, 'facebook_nodes_users', 'user_id', 'facebook_node_id')
            ->where('facebook_node', FacebookNodes::NODE_GROUP);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function facebookAlbums()
    {
        return $this->belongsToMany(FacebookNodes::class, 'facebook_nodes_users', 'user_id', 'facebook_node_id')
            ->where('facebook_node', FacebookNodes::NODE_ALBUM);
    }

    /**
     * @return mixed
     */
    public function fbTokenExpired()
    {
        return $this->facebook_access_token_expires ? $this->facebook_access_token_expires->lt(Carbon::now()) : true;
    }

    /**
     * @return mixed
     */
    public function getFacebookUserToken()
    {
        return $this->facebook_access_token;
    }

    /**
     * @return mixed
     */
    public function getFacebookUserId()
    {
        return $this->facebook_user_id;
    }

    /**
     * @return array
     */
    public function getFacebookGroups()
    {
        return $this->dispatchNow(new GetUserFacebookGroupsCommand($this));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referredBy()
    {
        return $this->belongsTo(self::class, 'referred_by_user_id');
    }

    /**
     * TODO: Make this only pull "qualifying" referrals
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function qualifyingReferrals()
    {
        return $this->referrals();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany(self::class, 'referred_by_user_id')->orderBy('created_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notificationsettings()
    {
        return $this->belongsToMany(Notifications::class, 'users_notificationsettings', 'user_id', 'notification_id')
            ->withPivot(['email', 'web', 'sms']);
    }

    /**
     * @return bool
     */
    public function getCard()
    {
        if (! $this->hasStripeId()) {
            return false;
        }

        $customer = $this->asStripeCustomer();

        $defaultCard = false;

        if ($customer->sources) {
            foreach ($customer->sources->data as $card) {
                if ($card->id === $customer->default_source) {
                    $defaultCard = $card;
                    break;
                }
            }
        }

        return $defaultCard;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function creditTransactions()
    {
        return $this->hasMany(CreditTransactionsLog::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creditBalance()
    {
        return $this->hasOne(CreditBalance::class, 'user_id');
    }

    /**
     * @return float
     */
    public function getAvailableBalance()
    {
        $balance = $this->creditBalance;

        return (float) ($balance ? $balance->balance : 0.00);
    }

    /**
     * @param $debitAmount
     *
     * @return bool
     */
    public function hasSufficientBalance($debitAmount)
    {
        $debitAmount = abs(intval($debitAmount));

        return ($this->getAvailableBalance() - $debitAmount) > 0;
    }

    /**
     * @param $debitAmount
     *
     * @return bool
     */
    public function hasSufficientCredits($debitAmount)
    {
        return $this->hasSufficientBalance($debitAmount);
    }

    /**
     * @return bool
     */
    public function usesKabooodleAsShipper()
    {
        return $this->kabooodle_as_shipping;
    }

    /**
     * @param string $subscription
     * @return bool
     */
    public function hasSubscriptionAccess(string $subscription = Plans::SUBSCRIPTION_MERCHANT)
    {
        if ($this->hasAtLeastMerchantSubscription()) {
            return true;
        }

        if ($this->subscribed($subscription)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function hasAtLeastMerchantSubscription()
    {
        return ($this->onGenericTrial() || $this->isSubscribedToMerchant() || $this->isSubscribedToMerchantPlus());
    }

    /**
     * @return bool
     */
    public function isSubscribedToMerchantPlus()
    {
        return ($this->onGenericTrial() || $this->subscribed(Plans::SUBSCRIPTION_MERCHANT_PLUS));
    }

    /**
     * @return bool
     */
    public function isSubscribedToMerchant()
    {
        return $this->subscribed(Plans::SUBSCRIPTION_MERCHANT);
    }

    /**
     * @return null
     */
    public function genericTrialEndsInDays()
    {
        if ($this->onGenericTrial()) {
            $now = Carbon::now(webUser()->timzeone);
            return $this->trial_ends_at->diff($now)->days < 1 ? 'today' : 'in '.$this->trial_ends_at->diffInDays($now).' days';
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEarlyAdapter()
    {
        return $this->hasOne(EarlyAdapters::class, 'user_id')->first();
    }

    /**
     * @return mixed
     */
    public function currentSubscription()
    {
        return $this->subscriptions->sortByDesc(function ($value) {
            return $value->created_at->getTimestamp();
        })->first();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function genericTrialHistory()
    {
        return $this->hasOne(GenericTrialHistory::class);
    }

    /**
     * @return bool|mixed
     */
    public function hasUserAlreadyHadGenericTrial()
    {
        $trial = $this->genericTrialHistory;
        $subscriptions = $this->subscriptions;

        return ($trial || $subscriptions->count() > 0) ? true : false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function watching()
    {
        return $this->hasMany(Watches::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function phoneNumber()
    {
        return $this->hasOne(PhoneNumber::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function viewed()
    {
        return $this->hasMany(View::class, 'viewer_id');
    }

    /**
     * @return mixed
     */
    public function usersFollowing()
    {
        return $this->morphedByMany(User::class, 'followable')->where('followables.deleted_at', null)->orderBy('username');
    }

    /**
     * @return mixed
     */
    public function flashsalesFollowing()
    {
        return $this->morphedByMany(FlashSales::class, 'followable')->where('followables.deleted_at', null);
    }

    /**
     * @return bool
     */
    public function myAccountIsAQualifyingReferral()
    {
        return ! $this->onGenericTrial() && $this->hasAtLeastMerchantSubscription();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function qualifiedReferrals()
    {
        return $this->hasMany(QualifiedReferrals::class, 'referred_id');
    }

    /**
     * @return mixed
     */
    public function pendingQualifiedReferrals()
    {
        return $this->qualifiedReferrals()->whereNull('coupon_applied_at');
    }

    /**
     * @return mixed
     */
    public function appliedQualifiedReferrals()
    {
        return $this->qualifiedReferrals()->whereNotNull('coupon_applied_at');
    }

    /**
     * @return int
     */
    public function getPendingQualifiedReferralTotal()
    {
        $qualifiedReferrals = $this->pendingQualifiedReferrals;
        if ($qualifiedReferrals->count() >= 6) {
            return 6;
        }

        return $qualifiedReferrals->count();
    }

    /**
     * @return string
     */
    public function getProfileUrlAttribute()
    {
        return route('user.profile', [$this->username]);
    }

    /**
     * @return null|string
     */
    public function hasKeenAccessKey()
    {
        return $this->keen_access_key;
    }

    /**
     * @return bool
     */
    public function isSuperUser()
    {
        return $this->superuser;
    }
}
