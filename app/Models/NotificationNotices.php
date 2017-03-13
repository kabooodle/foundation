<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Libraries\WebSockets\WebSocket;

/**
 * Class NotificationNotices
 */
class NotificationNotices extends BaseEloquentModel
{
    const PRIORITY_HIGH = 'high';
    const PRIORITY_MED = 'med';
    const PRIORITY_LOW = 'low';

    /**
     * @var string
     */
    protected $table = 'notification_user_notices';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => '',
        'notification_id' => null,
        'reference_id' => null,
        'reference_type' => null,
        'reference_url' => '',
        'title' => '',
        'description' => '',
        'payload' => null,
        'is_read' => 0,
        'priority' => self::PRIORITY_LOW
    ];

    /**
     * @var array
     */
    protected $hidden = [];

    public static function boot()
    {
        parent::boot();

        self::created(function(self $model){
            $event = new WebSocket;
            $event->setChannelName('private.'.env('APP_ENV').'.notices.'.$model->user->id)
                ->setEventName('created')
                ->setPayload([
                    'model' => $model->toArray()
                ])
                ->send();
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notification()
    {
        return $this->belongsTo(Notifications::class, 'notification_id');
    }
}
