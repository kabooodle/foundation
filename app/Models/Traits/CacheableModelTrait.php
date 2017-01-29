<?php

namespace Kabooodle\Models\Traits;

use Cache;

/**
 * Class CacheableModelTrait
 */
trait CacheableModelTrait
{
    public static function table()
    {
        $instance = new static;
        return $instance->getTable();
    }

    /**
     * @param array $options
     */
    public function save(array $options = [])
    {
        parent::save($options);
        Cache::tags($this->table)->flush();
    }

    /**
     * @param array $options
     */
    public function delete(array $options = [])
    {
        parent::delete($options);
        Cache::tags($this->table)->flush();
    }
}
