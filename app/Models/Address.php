<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class Address
 * @package Kabooodle\Models
 */
class Address extends BaseEloquentModel implements Revisionable
{
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
    protected $attributes = [
        'user_id' => 0,
        'type' => self::TYPE_FROM,
        'company' => null,
        'street1' => '',
        'street2' => null,
        'city' => '',
        'state' => '',
        'zip' => '',
        'country' => 'US',
        'phone' => null,
        'is_residential' => true,
        'metadata' => ''
    ];

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'from.street1' => 'required',
            'from.city' => 'required',
            'from.state' => 'required',
            'from.zip' => 'required',

            'to.street1' => '',
            'to.city' => 'required_with:street1',
            'to.state' => 'required_with:to.street1,to.city',
            'to.zip' => 'required_with:to.street1,to.city,to.state',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
