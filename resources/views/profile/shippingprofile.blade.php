@extends('profile.settingstemplate')

@section('settings-content')

    {{ Form::open(['route' => 'profile.shippingprofile.update', 'method' => 'POST']) }}
    @if(user()->subscribed('main'))
    <div class="box">
        <div class="box-header">
            <h2>Shipping Profile Settings</h2>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <div class="row">
                <div class="col-sm-9">
                    <p>Set {{env('APP_NAME')}} as default shipping provider.</p>
                    <small class="text-muted">When a claim is accepted, the claim is automatically added to {{ env('APP_NAME') }}'s shipping queue for you.</small>
                </div>
                <div class="col-sm-3">
                    <div class="checkbox  pull-right checkbox-slider--b-flat">
                        <label>
                            <input
                                    name="kabooodle_as_shipping"
                                    @if(user()->usesKabooodleAsShipper()) checked @endif
                                    type="checkbox"><span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h2>Shipping From Address</h2>
            <small>This is the address used as the "From" address when using shipping labels and processing shipments as a seller.</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            @include('profile.partials._addressform', ['_key' => 'from', '_from' => $from])
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
            @include('profile.partials._addressform', ['_key' => 'to', '_from' => $to])
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
        </div>
    </div>

    {{ Form::close() }}

@endsection