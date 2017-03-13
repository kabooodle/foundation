<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class FlashsaleGroups
 */
class FlashsaleGroups extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'flashsales_groups';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_id'
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'name' => 'required|unique:flashsales_groups,name',
            'users' => 'required|array'
        ];
    }

    /**
     * @return array
     */
    public static function getRuleMessages()
    {
        return [
            'name.unique' => 'Were sorry, this name appears to have been taken.',
            'name.required' => 'The group must have a name.',
            'users.required' => 'At least one seller must be added to a group.'
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'flashsales_groups_users', 'flashsales_group_id', 'user_id');
    }
}
