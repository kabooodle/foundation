<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class GroupsInvitations
 * @package Kabooodle\Models
 */
class Invitations extends BaseEloquentModel
{
    use ObfuscatesIdTrait, UuidableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'invitations';

    /**
     * @var array
     */
    protected $casts = [
        'accepted' => 'bool',
        'accepted_at' => 'datetime',
        'invited_by' => 'int',
        'invited_at' => 'datetime'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'invitable_id',
        'invitable_type',
        'invited_by',
        'user_id',
        'accepted_at',
        'accepted',
        'email'
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->invited_at = \Carbon\Carbon::now();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function invitable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invitedByUser()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function isExistingUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return mixed
     */
    public function getInvitedAtHuman()
    {
        return $this->invited_at->diffForHumans();
    }

    /**
     * @return mixed
     */
    public function getAcceptedAtHuman()
    {
        return $this->accepted_at->diffForHumans();
    }
}
