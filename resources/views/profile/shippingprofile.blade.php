@extends('profile.settingstemplate')

@section('settings-content')

    <div id="shipping-profile-div">
        <shipping-profile
                :is-merchant-plus="{{ json_encode(webUser()->isSubscribedToMerchantPlus()) }}"
                :initial-uses-kabooodle-as-shipper="{{ json_encode((bool) webUser()->usesKabooodleAsShipper()) }}"
                shipping-profile-update-endpoint="{{ apiRoute('user.shipping-profile.update', webUser()->id) }}"
                addresses-endpoint="{{ apiRoute('user.addresses.index', webUser()->id) }}"
                update-primary-endpoint="{{ apiRoute('user.addresses.update-primary', webUser()->id) }}"
                ship-from-type="{{ \Kabooodle\Models\Address::TYPE_FROM }}"
                :initial-from-addresses="{{ $fromAddresses->toJson() }}"
                :initial-primary-from-id="{{ $primaryFrom->id or 0 }}"
                ship-to-type="{{ \Kabooodle\Models\Address::TYPE_TO }}"
                :initial-to-addresses="{{ $toAddresses->toJson() }}"
                :initial-primary-to-id="{{ $primaryTo->id or 0 }}"
        ></shipping-profile>
    </div>

@endsection

@push('footer-scripts')
    <script src="{{ staticAsset('/assets/js/shipping-profile.js') }}"></script>
@endpush
