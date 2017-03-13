<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class FacebookNodes
 * @package Kabooodle\Models
 */
class FacebookNodes extends BaseEloquentModel
{
    const NODE_GROUP = 'group';
    const NODE_ALBUM = 'album';
    const NODE_PHOTO = 'photo';
    const NODE_COMMENT = 'comment';

    /**
     * @var string
     */
    protected $table = 'facebook_nodes';

    /**
     * @var array
     */
    protected $attributes = [
        'facebook_node_id' => 0,
        'facebook_parent_node_id' => null,
        'facebook_node_name' => '',
        'facebook_data' => '',
        'facebook_node_type' => self::NODE_ALBUM,
    ];

    public static function boot()
    {
        parent::boot();

        $handlers = ['updating', 'creating'];
        foreach ($handlers as $handler) {
            self::$handler(function ($model) {
                $model->updated_by = user()->id;
            });
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(FacebookNodes::class, 'facebook_parent_node_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'facebook_nodes_users', 'db_node_id', 'user_id');
    }
}
