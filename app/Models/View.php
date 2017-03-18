<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class View
 */
class View extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'views';

    /**
     * @var array
     */
    protected $fillable = [
        'viewer_id',
        'viewable_type',
        'viewable_id',
        'parent_id',
        'ip_address'
    ];

    public static function boot()
    {
        parent::boot();

        self::created(function($model) {
            $model->recordChildViews();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(View::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(View::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function viewable()
    {
        return $this->morphTo('viewable');
    }

    protected function recordChildViews()
    {
        if ($this->viewable && $this->viewable->hasViewableChild()) {
            $child = $this->viewable->getViewableChild();
            self::create([
                'viewer_id' => $this->viewer_id,
                'viewable_type' => get_class($child),
                'viewable_id' => $child->id,
                'ip_address' => $this->ip_address,
            ]);
        }
    }
}
