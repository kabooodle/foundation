<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Sofa\Revisionable\Revisionable;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;
use Kabooodle\Models\Contracts\ShoppableInterface;
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
    ShoppableInterface,
    Revisionable
{
    use AlgoliaEloquentTrait, Authenticatable, Authorizable, Billable, CanResetPassword, DispatchesJobs, LikeableTrait, FollowableTrait, ObfuscatesIdTrait, PresentableTrait, RevisionableTrait, SyncableGraphNodeTrait;

    /**
     * @var string
     */
    protected $presenter = UserModelPresenter::class;

    /**
     * @var array
     */
    protected $dates =[
        'created_at',
        'updated_at',
        'trial_ends_at',
        'facebook_access_token_expires'
    ];

    /**
     * Don't use "created" because it will fail a foreign key constraint. And this is erroneous anyway.
     *
     * @var array
     */
    protected static $revisionableEvents = [
        'Updated',
        'Deleted',
        'Restored'
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
        'activated' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'invited_by_user_id', 'activated', 'timezone', 'city', 'state'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'stripe_id', 'card_brand', 'card_last_four', 'trial_ends_at', 'pivot', 'activated', 'access_token', 'facebook_user_id'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($user){
            $user->username = self::_createUsername($user->name);
            $user->public_hash = self::_createHash();
        });

        self::saving(function($user){
            $user->email = trim(strtolower($user->email));
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
        return (bool) $this->activated;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'user_id');
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
     * @return mixed
     */
    public function flashsaleItems()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsale_items', 'seller_id', 'flashsale_id')->withTimestamps()->withPivot(['inventory_id', 'id as pivot_id']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facebookItems()
    {
        return $this->hasMany(FacebookItems::class, 'seller_id');
    }

    /**
     * @return mixed
     */
    public function flashsalesAsSeller()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsales_sellers', 'user_id', 'flashsales_id')->withTimestamps()->where('flashsales.active', 1);
    }

    /**
     * @return mixed
     */
    public function flashsalesAsAdmin()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsales_admins', 'user_id', 'flashsales_id')->withTimestamps()->where('flashsales.active', 1);
    }

    /**
     * @return null
     */
    public function flashsalesAsSellerAndAdmins()
    {
        $asSeller = $this->flashsalesAsSeller;
        $asAdmin = $this->flashsalesAsAdmin;

        if (count($asSeller) > 0) {
            return $asAdmin ? $this->asSeller->merge($asAdmin) : $this->flashsalesAsSeller();
        } elseif (count($asAdmin) > 0) {
            return $this->flashsalesAsAdmin();
        }

        return $this->flashsalesAsAdmin();
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
     * @return\Illuminate\Database\Eloquent\Relations\HasOne|ShippingAddress
     */
    public function shipFromAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'user_id')->where('type', ShippingAddress::TYPE_FROM);
    }

    /**
     * @return\Illuminate\Database\Eloquent\Relations\HasOne|ShippingAddress
     */
    public function shipToAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'user_id')->where('type', ShippingAddress::TYPE_TO);
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
    public function claimsOnMyInventory()
    {
        return $this->hasManyThrough(Claims::class,  Inventory::class)->where('inventory.user_id', $this->id);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsAsSeller()
    {
        return $this->claimsOnMyInventory();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claimsAsBuyer()
    {
        return $this->hasMany(Claims::class, 'claimed_by');
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
     * @return bool
     */
    public function getCard()
    {
        if (! $this->hasStripeId()) {
            return false;
        }

        $customer = $this->asStripeCustomer();

        $defaultCard = false;

        foreach ($customer->sources->data as $card) {
            if ($card->id === $customer->default_source) {
                $defaultCard = $card;
                break;
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

        return (float) ($balance ? $balance->sum('balance') : 0.00);
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
}
