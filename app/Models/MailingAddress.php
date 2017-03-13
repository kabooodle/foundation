<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class MailingAddress
 * @package Kabooodle\Models
 */
final class MailingAddress
{
    /**
     * @var null
     */
    public $company;

    /**
     * @var string
     */
    public $street1;

    /**
     * @var null|string
     */
    public $street2;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $zip;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var null|string
     */
    public $phone;

    /**
     * MailingAddress constructor.
     *
     * @param null          $company
     * @param string        $street1
     * @param null|string   $street2
     * @param string        $city
     * @param string        $state
     * @param string        $zip
     * @param string        $name
     * @param string        $email
     * @param null|string   $phone
     */
    public function __construct(
        $company = null,
        $street1,
        $street2 = null,
        $city,
        $state,
        $zip,
        $name,
        $email,
        $phone = null
    ) {
        $this->company = $company;
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * @return null
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'company' => $this->getCompany(),
            'street1' => $this->getStreet1(),
            'street2' => $this->getStreet2(),
            'city' => $this->getCity(),
            'state' => $this->getState(),
            'zip' => $this->getZip(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone()
        ];
    }

    /**
     * @param $array
     *
     * @return static
     */
    public static function fromArray($array)
    {
        return new static(
            array_get('company', $array),
            $array['street1'],
            array_get('street2', $array),
            $array['city'],
            $array['state'],
            $array['zip'],
            array_get($array, 'name'),
            $array['email'],
            array_get($array, 'phone')
        );
    }
}
