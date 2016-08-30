<?php

namespace Kabooodle\Models;

use Carbon\Carbon;
use Crypt;
use Kabooodle\User;
use GuzzleHttp\Cookie\SetCookie;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * Class LLRUser
 * @package Kabooodle\Models
 */
class LLRUser extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'llr_user';

    /**
     * @var array
     */
    protected $appends = [
        'decrypted_password',
        'cookie'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'encrypted_password',
        'last_attempted_on',
        'last_attempt_status',
        'cookie_string',
        'cookie_expires_timestamp',
        'cookie_expires_datetime'
    ];

    /**
     * @param $value
     */
    public function setEncryptedPasswordAttribute($value)
    {
        $this->attributes['encrypted_password'] = Crypt::encrypt($value);
    }

    /**
     * @return null|string
     */
    public function getDecryptedPasswordAttribute()
    {
        try {
            return Crypt::decrypt($this->encrypted_password);
        } catch (DecryptException $e) {
            return null;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return SetCookie
     */
    public function getCookieAttribute()
    {
        return SetCookie::fromString($this->cookie_string);
    }

    public function setCookieExpiresTimestampAttribute()
    {
        $this->attributes['cookie_expires_timestamp'] = $this->cookie['date']['Expires'];
    }

    public function setCookieExpiresDatetimeAttribute()
    {
        $this->attributes['cookie_expires_datetime'] = Carbon::createFromTimestamp($this->cookie['date']['Expires'])->toDateTimeString();
    }
}