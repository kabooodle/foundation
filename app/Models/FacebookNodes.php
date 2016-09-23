<?php

namespace Kabooodle\Models;

/**
 * Class FacebookNodes
 * @package Kabooodle\Models
 */
class FacebookNodes extends BaseEloquentModel
{
    CONST NODE_ALBUM = 'album';
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
//        'facebook_node_id' => 0,
//        'facebook_post_id' => 0,
//        'facebook_node_name' => '',
//        'facebook_data' => [],
//        'facebook_node' => self::NODE_ALBUM,
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|FacebookItems
     */
    public function albumItems()
    {
        return $this->hasMany(FacebookItems::class, 'facebook_post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}