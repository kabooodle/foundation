<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * Class Address
 * @package Kabooodle\Models
 */
class Email extends BaseEloquentModel
{
    use SoftDeletes;

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
        'verified' => 0,
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

    /**
     * @var array
     */
    protected $hidden = [
        'token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'primary' => 'boolean',
        'verified' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($email) {
            $email->token = Uuid::uuid4();
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
        return $this->primary;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return $this->verified;
    }

    /**
     * @return $this
     */
    public function verify()
    {
        $this->verified = true;
        $this->token = null;
        $this->save();

        return $this;
    }

    /**
     * Verify the email.
     *
     * @return bool
     */
    public function generateNewToken()
    {
        $this->token = Uuid::uuid4();
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
