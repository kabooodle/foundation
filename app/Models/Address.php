<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Revisionable;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class Address
 * @package Kabooodle\Models
 */
class Address extends BaseEloquentModel implements Revisionable
{
    use SoftDeletes;
    use RevisionableTrait;

    const TYPE_BILLING = 'billing';
    const TYPE_FROM = 'ship_from';
    const TYPE_TO = 'ship_to';

    /**
     * @var string
     */
    protected $table = 'addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'object_id',
        'type',
        'primary',
        'name',
        'company',
        'street1',
        'street2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'is_residential',
        'metadata',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'type' => self::TYPE_FROM,
        'name' => '',
        'company' => null,
        'street1' => '',
        'street2' => null,
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => 'US',
        'phone' => '',
        'is_residential' => true,
        'metadata' => ''
    ];

    /**
     * @var array
     */
    protected $casts = [
        'primary' => 'boolean',
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'street1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'phone' => 'required'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
