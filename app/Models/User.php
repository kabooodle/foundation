<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Laravel\Cashier\Billable;
use Sofa\Revisionable\Revisionable;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Kabooodle\Models\Traits\LikeableTrait;
use Illuminate\Database\Eloquent\Collection;
use Kabooodle\Models\Traits\FollowableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Kabooodle\Models\Contracts\LikeableInterface;
use Illuminate\Foundation\Auth\Access\Authorizable;
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
    use AlgoliaEloquentTrait, Authenticatable, Authorizable, Billable, CanResetPassword, LikeableTrait, FollowableTrait, ObfuscatesIdTrait, RevisionableTrait;

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
        'password', 'remember_token', 'stripe_id', 'card_brand', 'card_last_four', 'trial_ends_at', 'pivot', 'activated'
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
     * @return mixed
     */
    public function flashsalesAsSeller()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsales_sellers', 'user_id', 'flashsales_id')->withTimestamps()->where('flashsales.active', 1);
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
}
