<?php
if (!function_exists('logDBQuerues')) {
    function logDbQuerues()
    {
        DB::enableQueryLog();
        DB::listen(
            function ($sql) {
                // $sql is an object with the properties:
                //  sql: The query
                //  bindings: the sql query variables
                //  time: The execution time for the query
                //  connectionName: The name of the connection

                // To save the executed queries to file:
                // Process the sql and the bindings:
                foreach ($sql->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $sql->bindings[$i] = "'$binding'";
                        }
                    }
                }

                // Insert bindings into query
                $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                $query = vsprintf($query, $sql->bindings);

                // Save the query to file
                $logFile = fopen(
                    storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . '_query.log'),
                    'a+'
                );
                fwrite($logFile, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);
                fclose($logFile);
            }
        );
    }
}


if (! function_exists('shippingStatii')) {
    /**
     * @return array
     */
    function shippingStatii()
    {
        return array_combine(\Kabooodle\Models\ShippingTransactions::SHIPPING_STATII, \Kabooodle\Models\ShippingTransactions::SHIPPING_STATII);
    }
}

if (! function_exists('salesStatii')) {
    /**
     * @return array
     */
    function salesStatii()
    {
        $data = ['PENDING LABEL CREATION', 'EXTERNALLY SHIPPED'];

        return array_combine($data, $data) + shippingStatii();
    }
}

if (! function_exists('categoriesInUse')) {
    /**
     * @return mixed
     */
    function categoriesInUse()
    {
        return dispatchNow(new \Kabooodle\Bus\Commands\Inventory\GetUsedInventoryCategoriesCommand(user()));
    }
}
if (! function_exists('llrSizes')) {
    /**
     * @return mixed
     */
    function llrSizes()
    {
        return dispatchNow(new \Kabooodle\Bus\Commands\Inventory\GetLLRSizesCommand);
    }
}

if (! function_exists('llrStyles')) {
    /**
     * @return mixed
     */
    function llrStyles()
    {
        return dispatchNow(new \Kabooodle\Bus\Commands\Inventory\GetLLRStylesCommand);
    }
}

if (! function_exists('hasSufficientBalance')) {
    /**
     * @param \Kabooodle\Models\User $user
     * @param                        $debitAmount
     *
     * @return bool
     */
    function hasSufficientBalance(\Kabooodle\Models\User $user, $debitAmount)
    {
        return $user->hasSufficientBalance($debitAmount);
    }
}

if (! function_exists('creditTypes')) {
    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function creditTypes()
    {
        static $creditTypes;
        if (! $creditTypes) {
            $creditTypes = \Kabooodle\Models\CreditChargeTypes::where('active', 1)->get();
        }

        return $creditTypes;
    }
}

if (! function_exists('rateAddon')) {
    /**
     * @return float
     */
    function rateAddon()
    {
        return \Kabooodle\Models\ShippingTransactions::RATE_ADDON;
    }
}

if (! function_exists('getParcelListByCarrier')) {
    /**
     * @param bool $returnCollection
     *
     * @return array|\Illuminate\Support\Collection
     */
    function getParcelListByCarrier($returnCollection = false)
    {
        $templates = dispatchNow(new \Kabooodle\Bus\Commands\Shipping\GetShippingParcelTemplatesCommand);

        return $returnCollection === false ? $templates->pluck('name_with_dimensions', 'parcel_id')->toArray() : $templates;
    }
}