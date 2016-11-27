<?php

namespace Kabooodle\Models;

use Kabooodle\Presenters\Models\Listings\ListingsModelPresenter;
use Kabooodle\Presenters\PresentableTrait;
use Nubs\RandomNameGenerator\Alliteration;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Listings
 */
class Listings extends AbstractListingModel
{
    use PresentableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var string
     */
    protected $presenter = ListingsModelPresenter::class;

    /**
     * @var array
     */
    protected $with = [
        'items'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'scheduled_for',
        'status_updated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var string
     */
    protected $table = 'listings';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'scheduled_for' => '',
        'owner_id' => 0,
        'fb_group_node_id' => null,
        'flashsale_id' => null,
        'uuid' => '',
        'type' => self::TYPE_FACEBOOK,
        'status' => self::STATUS_SCHEDULED,
        'status_updated_at' => null,
        'status_history' => '',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'scheduled_for',
        'fb_group_node_id',
        'flashsale_id',
        'owner_id',
        'type',
        'status',
        'status_updated_at',
        'status_history'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function(self $model){
            if(!$model->name || is_null($model->name)) {
                $model->name = $model->generateRandomName($model);
            }
        });
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function generateRandomName(self $model)
    {
        $name = with(new Alliteration)->getName();
        if (self::where('name', $name)->where('owner_id', $model->owner_id)->first()) {
            return $this->generateRandomName($model);
        }

        return $name;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listingItems()
    {
        return $this->items();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(ListingItems::class, 'listing_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }


    public function sales()
    {
        //
    }

    /**
     * @return int
     */
    public function albumsCount()
    {
        if ($this->isFacebook()) {
            return $this->items()->distinct('fb_album_node_id')->groupBy('fb_album_node_id')->get()->count();
        }

        return 0;
    }
}
