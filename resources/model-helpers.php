<?php
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