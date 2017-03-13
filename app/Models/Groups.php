<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class Groups
 * @package Kabooodle\Models
 */
class Groups extends BaseEloquentModel implements Revisionable
{
    use LikeableTrait, FollowableTrait, ObfuscatesIdTrait, SoftDeletes, RevisionableTrait;

    const PRIVACY_PUBLIC = 'public';
    const PRIVACY_SECRET = 'secret';
    const PRIVACY_PRIVATE = 'private';

    /**
     * @var array
     */
    protected $appends = [
        'is_followed',
    ];

    /**
     * @var array
     */
    protected $with = [
        'followers',
        'members',
        'admins',
        'activeFlashSales'
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function ($group) {
            $group->followers()->save(user());
        });
    }

    /**
     * @return array
     */
    public function getAlgoliaRecord()
    {
        return array_merge($this->toArray(), [
            'oid' => $this->getUUID(),
            'route' => route('groups.show', [$this->getUUID()])
        ]);
    }

    /**
     * @var string
     */
    protected $table = 'groups';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'description' => '',
        'privacy' => 'private',
        'published' => false
    ];

    /**
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'privacy' => 'string',
        'published' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'privacy'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'name' => 'required|unique:groups,name',
            'members' => 'array',
        ];
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public static function factory(array $attributes)
    {
        return self::create($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->morphMany(Invitations::class, 'invitable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'groups_members', 'group_id', 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'groups_admins', 'group_id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function allMembers()
    {
        return $this->members->merge($this->admins);
    }

    /**
     * @param $userId
     *
     * @return mixed|bool
     */
    public function isUserAdmin($userId)
    {
        return $this->admins->find($userId);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeFlashSales()
    {
        return $this->hasMany(FlashSales::class, 'host_id')->where('active', 1)->where('type', FlashSales::TYPE_GROUP);
    }
}
