<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Crypt;
use Illuminate\Contracts\Encryption\EncryptException;

/**
 * Class ShoppableTrait
 */
trait ShoppableTrait
{
    /**
     * @return string
     */
    public function makeHashedResourceString()
    {
        return Crypt::encrypt(get_called_class() . '::' . $this->id);
    }

    /**
     * @param $hash
     *
     * @return array
     * @throws EncryptException
     */
    public function decryptHashedResource($hash)
    {
        $decrypted = Crypt::decrypt($hash);
        $class = strtok($decrypted, '::');
        $id = substr($decrypted, strpos($decrypted, "::") + 1);

        if(! class_exists($class)){
            throw new EncryptException('Hashed resource given does not exist');
        }

        return [$class, $id];
    }
}