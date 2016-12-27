@extends('profile.settingstemplate')

@section('settings-content')

    <div id="shipping-profile-div">
        <shipping-profile
                :is-merchant-plus="{{ json_encode(user()->isSubscribedToMerchantPlus()) }}"
                :initial-uses-kabooodle-as-shipper="{{ json_encode((bool) user()->usesKabooodleAsShipper()) }}"
                shipping-profile-update-endpoint="{{ apiRoute('user.shipping-profile.update', user()->id) }}"
                addresses-endpoint="{{ apiRoute('user.addresses.index', user()->id) }}"
                update-primary-endpoint="{{ apiRoute('user.addresses.update-primary', user()->id) }}"
                :initial-from-addresses="{{ $fromAddresses->toJson() }}"
                :initial-primary-from-id="{{ $primaryFrom->id or 0 }}"
                :initial-to-addresses="{{ $toAddresses->toJson() }}"
                :initial-primary-to-id="{{ $primaryTo->id or 0 }}"
        ></shipping-profile>
    </div>
    @if(user()->isSubscribedToMerchantPlus())

    <div class="box">
        <div class="box-header">
            <h2>Shipping From Address</h2>
            <small>This is the address used as the "From" address when using shipping labels and processing shipments as a seller.</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            {{--@include('profile.partials._addressform', ['_key' => 'from', '_from' => $from])--}}
        </div>
    </div>
    @endif

    <div class="box">
        <div class="box-header">
            <h2>Shipping To Address</h2>
            <small>As a buyer, this is the shipping address used for the items you purchase.</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            {{--@include('profile.partials._addressform', ['_key' => 'to', '_from' => $to])--}}
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>


@endsection

@push('footer-scripts')
<script src="/assets/js/shipping-profile.js"></script>
@endpush
