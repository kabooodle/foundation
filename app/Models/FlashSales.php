<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Sofa\Revisionable\Revisionable;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\ClaimableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Kabooodle\Models\Traits\AuthorableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;
use Kabooodle\Presenters\Models\Flashsales\FlashsaleModelPresenter;

/**
 * Class FlashSales
 * @package Kabooodle\Models
 */
class FlashSales extends BaseEloquentModel implements LikeableInterface, Revisionable
{
    use AlgoliaEloquentTrait,
        AuthorableTrait,
        ClaimableTrait,
        FollowableTrait,
        LikeableTrait,
        ObfuscatesIdTrait,
        PresentableTrait,
        RevisionableTrait,
        SerializesModels,
        SoftDeletes;

    const TYPE_SINGLE = 'single';
    const TYPE_GROUP = 'group';

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'starts_at',
        'ends_at',
        'discount_starts_at',
        'discount_ends_at'
    ];

    /**
     * @var array
     */
    protected $appends = [
        'is_liked'
    ];

    /**
     * @var array
     */
    protected $with = [
        'likes',
    ];

    /**
     * @var string
     */
    protected $presenter = FlashsaleModelPresenter::class;

    /**
     * @param $indexName
     *
     * @return bool
     */
    public function indexOnly($indexName)
    {
        return $this->privacy == 'public';
    }

    /**
     * @return array
     */
    public function getAlgoliaRecord()
    {
        return array_merge($this->toArray(), [
            'oid' => $this->getUUID(),
            'route' => route('flashsales.show', [$this->getUUID()])
        ]);
    }

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'type' => 'single',
        'name' => '',
        'description' => '',
        'discount_percent' => 0,
        'discount_starts_at' => null,
        'discount_ends_at' => null,
        'active' => 0,
        'starts_at' => null,
        'ends_at' => null,
        'host_id' => null,
        'privacy' => 'public',
        'seller_rules' => ''
    ];

    /**
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'type' => 'string',
        'name' => 'string',
        'description' => 'string',
        'discount_percent' => 'int',
        'discount_starts_at' => 'date',
        'discount_ends_at' => 'date',
        'active' => 'bool',
        'starts_at' => 'date',
        'ends_at' => 'date',
        'privacy' => 'string',
        'seller_rules' => 'string',
        'host_id' => 'int'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'description',
        'discount_percent',
        'discount_starts_at',
        'discount_ends_at',
        'active',
        'starts_at',
        'ends_at',
        'host_id',
        'privacy',
        'seller_rules'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'deleted_by',
        'pivot'
    ];

    /**
     * @var string
     */
    protected $table = 'flashsales';

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'type' => 'required|in:'.self::TYPE_GROUP.','.self::TYPE_SINGLE,
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
//            'host_id' => 'exists:groups,id',
            'privacy' => 'required|in:private,public,secret'
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            'Myself' => self::TYPE_SINGLE,
            'A Group' => self::TYPE_GROUP
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function (self $model) {
            $model->active = 1;
        });

        self::created(function (self $model) {
            $model->admins()->save($model->owner);
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
     * @param $scope
     *
     * @return mixed
     */
    public function scopeWithoutExpired($scope)
    {
        return $scope->where('ends_at', '>', DB::raw('NOW()'));
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    public function scopeWithoutSecret($scope)
    {
        return $scope->where('privacy', '<>', 'secret');
    }

    /**
     * @param $v
     *
     * @return Carbon
     */
    public function getStartsAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));
    }

    /**
     * @param $v
     *
     * @return Carbon
     */
    public function getEndsAtAttribute($v)
    {
        return Carbon::createFromFormat(DATE_ISO8601, $this->convertDateTimeTo8601($v));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function coverimage()
    {
        return $this->morphOne(Images::class, 'imageable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->owner();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(Groups::class, 'host_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->hostIsGroup() ? $this->group() : $this->belongsTo(User::class, 'host_id');
    }

    /**
     * @return bool
     */
    public function hostIsGroup()
    {
        return $this->type == self::TYPE_GROUP;
    }

    /**
     * This is protected because admins should also include the creator of the flash sale.
     * The public method combines this collection and the other user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    protected function onlyAdmins()
    {
        return $this->belongsToMany(User::class, 'flashsales_admins', 'flashsales_id', 'user_id')->withTimestamps();
    }

    /**
     * TODO: Identify better way for returning a collection of the admins + owner.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        //        $owner = $this->owner->toArray();
//        $admins = $this->onlyAdmins->toArray();
//        return collect($owner)->merge(collect($admins));
        return $this->onlyAdmins();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sellers()
    {
        return $this->belongsToMany(User::class, 'flashsales_sellers', 'flashsales_id', 'user_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->morphMany(Invitations::class, 'invitable')->orderBy('invited_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'flashsale_items', 'flashsale_id', 'inventory_id')->withTimestamps()->withPivot('inventory_id');
    }

    /**
     * TODO: Identify a way to check whether the item was enabled or enabled by date.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function enabledInventoryItems()
    {
        return $this->inventoryItems();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pendingInvitations()
    {
        return $this->invitations()->where('accepted', 0);
    }

    /**
     * @return mixed
     */
    public function claims()
    {
        return $this->morphMany(Claims::class, 'shoppable');
    }

    /**
     * @return mixed
     */
    public function adminsAndSellers()
    {
        return $this->admins->merge($this->sellers);
    }

    /**
     * @return mixed
     */
    public function startsAtHuman()
    {
        return $this->starts_at->format('M d \'y \\a\\t h:ia');
    }

    /**
     * @return mixed
     */
    public function endsAtHuman()
    {
        return $this->ends_at->format('M d \'y \\a\\t h:ia');
    }

    /**
     * @return mixed
     */
    public function startsAtPicker()
    {
        return $this->starts_at->format('m/d/Y h:mA');
    }

    /**
     * @return mixed
     */
    public function endsAtPicker()
    {
        return $this->ends_at->format('m/d/Y h:mA');
    }

    /**
     * @return bool
     */
    public function saleHasStarted()
    {
        return $this->starts_at->lte(Carbon::now());
    }

    /**
     * @return bool
     */
    public function saleHasEnded()
    {
        return $this->ends_at->lt(Carbon::now());
    }

    /**
     * @return bool
     */
    public function saleIsActive()
    {
        return (bool) $this->saleHasStarted() && ! $this->saleHasEnded();
    }

    /**
     * @return bool
     */
    public function saleIsYetToStart()
    {
        return (bool) ! $this->saleHasStarted() && ! $this->saleHasEnded();
    }

    /**
     * @param User|null $user
     *
     * @return bool|User
     */
    public function userIsAdminOrSeller(User $user = null)
    {
        if (!$user || is_null($user)) {
            return false;
        }

        $sellersAndAdmins = $this->adminsAndSellers();

        return $sellersAndAdmins->filter(function ($user) use ($user) {
            return $user->id == $user->id;
        })->first();
    }
}
