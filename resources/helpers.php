<?php
if (! function_exists('appName')) {
    /**
     * @return mixed
     */
    function appName()
    {
        return env('APP_NAME', 'Kabooodle');
    }
}

if (! function_exists('getTld')) {
    /**
     * @return string
     */
    function getTld()
    {
        return substr(Request::root(), strrpos(Request::root(), ".") + 1);
    }
}

if (! function_exists('getEnvDomain')) {
    /**
     * @param bool $withTld
     *
     * @return mixed|string
     */
    function getEnvDomain($withTld = false)
    {
        $name = request()->server->get('HTTP_HOST');

        if (filter_var($name, FILTER_VALIDATE_IP) or is_numeric($name)) {
            return $name;
        }

        $array = explode(".", $name);

        $name = count($array) >= 3 ? $array[1] : $array[0];

        return $withTld ? $name . '.'.getTld() : $name;
    }
}

if (! function_exists('getProtocol')) {
    /**
     * http://stackoverflow.com/a/9031882
     *
     * @param bool $includeBackSlashes
     * @return string
     */
    function getProtocol($includeBackSlashes = true)
    {
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        return $includeBackSlashes ? $protocol .'://' : $protocol;
    }
}

if (! function_exists('getAppVersion')) {
    /**
     * @return string
     */
    function getAppVersion()
    {
        return Kabooodle\Foundation\Application\KabooodleApplication::APP_VERSION;
    }
}

if (! function_exists('user')) {
    /**
     * @return \Kabooodle\Models\User|null
     */
    function user()
    {
        return Auth::user();
    }
}