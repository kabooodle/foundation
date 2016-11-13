<?php
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