<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\Contracts\WatchableInterface;
use Kabooodle\Models\Traits\WatchableTrait;
use Sofa\Revisionable\Revisionable;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Presenters\PresentableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Kabooodle\Models\Traits\AuthorableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Presenters\Models\Flashsales\FlashsaleModelPresenter;

/**
 * Class FlashSales
 * @package Kabooodle\Models
 */
class FlashSales extends BaseEloquentModel implements Revisionable, WatchableInterface
{
    use AuthorableTrait,
        FollowableTrait,
        ObfuscatesIdTrait,
        PresentableTrait,
        RevisionableTrait,
        SerializesModels,
        SoftDeletes,
        WatchableTrait;

    const HOST_SELF = 'self';
    const HOST_GROUP = 'group';

    /**
     * @var array
     */
    protected $with = [
        'coverimage'
    ];

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
        'is_watched',
        'id_to_string',
        'uuid',
        'sellers',
        'claimable_range',
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
            'name' => 'required|unique:flashsales,name',
            'description' => '',
            'cover_photo' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
//            'hosted_by' => 'required|in:group,self',
//            'host_id' => 'exists:groups,id',
            'privacy' => 'required|in:private,public'
        ];
    }

    /**
     * @return array
     */
    public static function getUpdateRules()
    {
        return [
            'name' => 'required',
            'description' => '',
            'cover_photo' => 'required',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
            'privacy' => 'required|in:private,public'
        ];
    }

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [
            'Myself' => self::HOST_SELF,
            'A Group' => self::HOST_GROUP
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function (self $model) {
            $model->active = 1;
        });

        self::created(function (self $model) {
//            $model->admins()->save($model->owner);
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
        return $scope->where('ends_at', '>=', DB::raw('NOW()'));
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    public function scopeNotYetEnded($scope)
    {
        return $scope->where('starts_at', '>=',  DB::raw('NOW()'));
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    public function scopeOrderByStartDate($scope)
    {
        return $scope->orderBy('starts_at', 'asc');
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sellerGroups()
    {
        return $this->belongsToMany(FlashsaleGroups::class, 'flashsales_sellers_groups', 'flashsale_id',  'flashsale_group_id')
            ->withPivot('time_slot')
            ->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function getSellersAttribute()
    {
        return $this->sellers();
    }

    /**
     * @return mixed
     */
    public function sellers()
    {
        $sellers = collect([$this->owner]);
        if($admins = $this->admins) {
            foreach($admins as $admin) {
                $sellers->push($admin);
            }
        }

        $groups = $this->sellerGroups;
        if ($groups) {
            foreach($groups as $group) {
                foreach($group->users as $user) {
                    $sellers->push($user);
                }
            }
        }

        return $sellers;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function coverimage()
    {
        return $this->morphOne(Files::class, 'fileable');
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
    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invitations()
    {
        return $this->morphMany(Invitations::class, 'invitable')->orderBy('invited_at', 'desc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listing()
    {
        return $this->hasOne(Listings::class, 'flashsale_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function listingItems()
    {
        return $this->hasManyThrough(ListingItems::class, Listings::class, 'flashsale_id', 'listing_id');
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
        return $this->morphMany(Claims::class, 'listable');
    }

    /**
     * @return mixed
     */
    public function adminsAndSellers()
    {
        return $this->admins->merge($this->sellers);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function admins()
    {
        return $this->belongsToMany(User::class, 'flashsales_admins', 'flashsale_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return mixed
     */
    public function startsAtHuman()
    {
        return $this->starts_at->format('M d \\a\\t h:ia');
    }

    /**
     * @return mixed
     */
    public function endsAtHuman()
    {
        return $this->ends_at->format('M d \\a\\t h:ia');
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
        return $this->starts_at->lte(Carbon::now(current_timezone()));
    }

    /**
     * @return bool
     */
    public function saleHasEnded()
    {
        return $this->ends_at->lt(Carbon::now(current_timezone()));
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

    /**
     * @param $userId
     *
     * @return bool
     */
    public function canSellerListToFlashsaleAnytime($userId)
    {
        return $this->owner->id == $userId || $this->admins->filter(function($admin) use ($userId) { return $admin->id == $userId; });
    }

    /**
     * @param $userId
     *
     * @return mixed
     */
    public function getFlashsaleSellerGroupForUser($userId)
    {
        return $this->sellerGroups->filter(function ($group) use ($userId) {
            return $group->users->whereLoose('id', $userId);
        })->first();
    }

    /**
     * @return mixed
     */
    public function getIdToStringAttribute()
    {
        return $this->obfuscateIdToString();
    }

    /**
     * @return mixed
     */
    public function getUUIDAttribute()
    {
        return $this->getUUID();
    }

    /**
     * @return string
     */
    public function getClaimableRangeAttribute()
    {
        return $this->present()->getDateRange();
    }

    /**
     * @return bool
     */
    public function claimableBasedOnSchedule()
    {
        $now = Carbon::now(current_timezone());

        if ($now >= $this->starts_at && $now <= $this->ends_at) {
            return true;
        }

        return false;
    }

    /**
     * @param null $user
     *
     * @return bool
     */
    public function canUserViewPrivateSale($user = null)
    {
        $guest = ! $user;
        if ($this->privacy == 'private') {
            return $guest ? false : $this->sellers->where('id', $user->id);
        }

        return true;
    }
}
