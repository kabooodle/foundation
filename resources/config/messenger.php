<?php

return [

    'user_model' => Kabooodle\Models\User::class,

    'message_model' => Kabooodle\Models\ThreadMessages::class,

    'participant_model' => Kabooodle\Models\ThreadParticipants::class,

    'thread_model' => Kabooodle\Models\Threads::class,

    /**
     * Define custom database table names.
     */
    'messages_table' => 'messenger_messages',
    'participants_table' => 'messenger_participants',
    'threads_table' => 'messenger_threads',
];
