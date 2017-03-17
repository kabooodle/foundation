<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Queues
 */
class Queues extends BaseEloquentModel
{
    use SoftDeletes;

    CONST STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_RETRYING = 'retrying';
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILED = 'failed';

    /**
     * @var array
     */
    protected $dates = [
        'status_updated_at',
    ];

    /**
     * @var string
     */
    protected $table = 'queues';

    /**
     * @var array
     */
    protected $attributes = [
        'queueable_type' => null,
        'queuable_id' => null
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'queue',
        'queue_group',
        'owner_id',
        'payload',
        'status',
        'attempts',
        'queueable_type',
        'queuable_id',
        'status_updated_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->user();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
