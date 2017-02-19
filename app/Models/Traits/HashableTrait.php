<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Crypt;
use Illuminate\Contracts\Encryption\EncryptException;

/**
 * Class HashableTrait
 */
trait HashableTrait
{
    /**
     * @return string
     */
    public function makeHashedResourceString(): string
    {
        return Crypt::encrypt(get_called_class() . '::' . $this->id);
    }

    /**
     * @return string
     */
    public function getHashIdAttribute(): string
    {
        return $this->makeHashedResourceString();
    }
}
