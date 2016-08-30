<?php

namespace Kabooodle\Http\Controllers\Api;

/**
 * Class GeneralController
 * @package Kabooodle\Http\Controllers\Api
 */
class GeneralController extends AbstractApiController
{
    /**
     * Ping endpoint allows API consumers to check the version.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ping()
    {
        return $this->item('Pong!');
    }

    /**
     * Endpoint to show the Cachet version.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function version()
    {
        $latest = getAppVersion();

        return $this->setMetaData([
            'latest'    => $latest,
        ])->item(getAppVersion());
    }
}
