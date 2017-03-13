<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Tiny;

/**
 * Class ObfuscatesIdTrait
 * @package Kabooodle\Models\Traits
 */
trait ObfuscatesIdTrait
{
    /**
     * @var string
     */
    public $obfuscatesSeparator = '__';

    /**
     * @return string
     */
    public function obfuscateToURIStringFromModel()
    {
        return $this->obfuscateToURIString($this->id, $this->name);
    }

    /**
     * @param $id
     * @param $string
     *
     * @return string
     */
    public function obfuscateToURIString($id, $string)
    {
        return str_slug($string) . $this->obfuscatesSeparator .  $this->obfuscateIdToString($id);
    }

    /**
     * @param $string
     *
     * @return int
     */
    public function obfuscateFromURIString($string)
    {
        return (int) $this->obfuscateStringToId(substr($string, strpos($string, $this->obfuscatesSeparator) + 1));
    }

    /**
     * @return mixed
     */
    public function obfuscateIdToString($id = null)
    {
        return Tiny::to($id ? : $this->id);
    }

    /**
     * @param $string
     *
     * @return int
     */
    public function obfuscateStringToId($string)
    {
        return (int) Tiny::from($string);
    }

    /**
     * @return string
     */
    public function getUUID()
    {
        return $this->obfuscateToURIStringFromModel();
    }

    /**
     * @return string
     */
    public function getObfuscateIdAttribute(): string
    {
        return $this->obfuscateToURIStringFromModel();
    }
}
