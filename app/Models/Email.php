<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class Address
 * @package Kabooodle\Models
 */
class Email extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'emails';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'address' => '',
        'primary' => 0,
        'verified' => null,
        'token' => null,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'address',
        'primary',
        'verified',
        'token',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($email) {
            $email->token = str_random(30);
        });

        self::saving(function ($email) {
            $email->address = trim(strtolower($email->address));
        });
    }

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'address' => 'required|email',
        ];
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
     * @return bool
     */
    public function isPrimary()
    {
        return (bool) $this->primary;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return (bool) $this->verified;
    }

    /**
     * Verify the email.
     *
     * @return bool
     */
    public function verify()
    {
        $this->verified = true;
        $this->token = null;
        return $this->save();
    }

    /**
     * Verify the email.
     *
     * @return bool
     */
    public function generateNewToken()
    {
        $this->token = str_random(30);
        return $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
