<?php
if (! function_exists('getParcelsListUSPS')) {
    /**
     * @return Array
     */
    function getParcelListByCarrier()
    {
        $model = \Kabooodle\Models\ShippingParcelTemplates::orderBy('name')->get();

        return $model->pluck('name_with_dimensions', 'parcel_id')->toArray();
    }
}

if (! function_exists('getSateAbbrevs')) {
    /**
     * @return array
     */
    function getStateAbbrevs()
    {
        return [
            '' => '',
            'AL'=>'ALABAMA',
            'AK'=>'ALASKA',
            'AS'=>'AMERICAN SAMOA',
            'AZ'=>'ARIZONA',
            'AR'=>'ARKANSAS',
            'CA'=>'CALIFORNIA',
            'CO'=>'COLORADO',
            'CT'=>'CONNECTICUT',
            'DE'=>'DELAWARE',
            'DC'=>'DISTRICT OF COLUMBIA',
            'FM'=>'FEDERATED STATES OF MICRONESIA',
            'FL'=>'FLORIDA',
            'GA'=>'GEORGIA',
            'GU'=>'GUAM GU',
            'HI'=>'HAWAII',
            'ID'=>'IDAHO',
            'IL'=>'ILLINOIS',
            'IN'=>'INDIANA',
            'IA'=>'IOWA',
            'KS'=>'KANSAS',
            'KY'=>'KENTUCKY',
            'LA'=>'LOUISIANA',
            'ME'=>'MAINE',
            'MH'=>'MARSHALL ISLANDS',
            'MD'=>'MARYLAND',
            'MA'=>'MASSACHUSETTS',
            'MI'=>'MICHIGAN',
            'MN'=>'MINNESOTA',
            'MS'=>'MISSISSIPPI',
            'MO'=>'MISSOURI',
            'MT'=>'MONTANA',
            'NE'=>'NEBRASKA',
            'NV'=>'NEVADA',
            'NH'=>'NEW HAMPSHIRE',
            'NJ'=>'NEW JERSEY',
            'NM'=>'NEW MEXICO',
            'NY'=>'NEW YORK',
            'NC'=>'NORTH CAROLINA',
            'ND'=>'NORTH DAKOTA',
            'MP'=>'NORTHERN MARIANA ISLANDS',
            'OH'=>'OHIO',
            'OK'=>'OKLAHOMA',
            'OR'=>'OREGON',
            'PW'=>'PALAU',
            'PA'=>'PENNSYLVANIA',
            'PR'=>'PUERTO RICO',
            'RI'=>'RHODE ISLAND',
            'SC'=>'SOUTH CAROLINA',
            'SD'=>'SOUTH DAKOTA',
            'TN'=>'TENNESSEE',
            'TX'=>'TEXAS',
            'UT'=>'UTAH',
            'VT'=>'VERMONT',
            'VI'=>'VIRGIN ISLANDS',
            'VA'=>'VIRGINIA',
            'WA'=>'WASHINGTON',
            'WV'=>'WEST VIRGINIA',
            'WI'=>'WISCONSIN',
            'WY'=>'WYOMING',
            'AE'=>'ARMED FORCES AFRICA \ CANADA \ EUROPE \ MIDDLE EAST',
            'AA'=>'ARMED FORCES AMERICA (EXCEPT CANADA)',
            'AP'=>'ARMED FORCES PACIFIC'
        ];
    }
}






if (! function_exists('apiRoute')) {
    /**
     * @param        $routeName
     * @param array  $routeParams
     * @param string $version
     * @param bool   $absoluteUrl
     *
     * @return string
     */
    function apiRoute($routeName, $routeParams = [], $version = 'v1', $absoluteUrl = true)
    {
        return app('Dingo\Api\Routing\UrlGenerator')->version($version)->route($routeName, $routeParams, $absoluteUrl);
    }
}

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