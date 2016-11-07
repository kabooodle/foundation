@extends('layouts.full', ['contentId' => 'shipping_create'])


@section('body-menu')
    @include('shipping.order.partials._bodynav')
@endsection


@section('body-content')
    <div class="row">
        <div class="col-md-12">
            {{ Form::open(['route' => ['shipping.store', user()->username]]) }}
            <div class="box white">
                <div class="box-header">
                    <h4>Claimed Item Information</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <div class="form-group row {{ $errors->has('claim_id') ? 'has-danger' : null }}" id="packaging-wrapper">
                        <label class="form-control-label col-sm-3">Claim Reference</label>
                        <div class="col-sm-6">
                            {{ Form::select('claim_id[]', [], Binput::get('c', null), ['class' => 'disabled form-control', 'disabled', 'id' => 'claimer_select_el', '@change' => 'claimReferenceChanged'])  }}
                            <div id="claimed_items_container"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box white">
                <div class="box-header">
                    <h4>Parcel Information</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <div class="form-group row {{ $errors->has('parcel.id') ? 'has-danger' : null }}" id="packaging-wrapper">
                        <label class="form-control-label col-sm-3">Packaging</label>
                        <div class="col-sm-9">
                            {{ Form::select('parcel[id]', [], null, ['data-size' => 'auto', 'data-width' => '100%', 'class' => 'disabled form-control', 'disabled', 'id' => 'parcel_el', '@change' => 'packagingChanged'])  }}
                        </div>
                    </div>
                    <div id="packaging-self-wrapper">
                        <div class="form-group row {{ $errors->has('parcel.length') ? 'has-danger' : null }}">
                            <label class="form-control-label col-sm-3">Length</label>
                            <div class="col-sm-3">
                                {{ Form::number('parcel[length]', null, ['class' => 'form-control numberic float']) }}
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('parcel.width') ? 'has-danger' : null }}">
                            <label class="form-control-label col-sm-3">Width</label>
                            <div class="col-sm-3">
                                {{ Form::number('parcel[width]', null, ['class' => 'form-control numberic float']) }}
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('parcel.height') ? 'has-danger' : null }}">
                            <label class="form-control-label col-sm-3">Height</label>
                            <div class="col-sm-3">
                                {{ Form::number('parcel[height]', null, ['class' => 'form-control numberic float']) }}
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('parcel.dimensions_uom') ? 'has-danger' : null }}">
                            <label class="form-control-label col-sm-3">Units</label>
                            <div class="col-sm-3">
                                {{ Form::select('parcel[distance_unit]', \Kabooodle\Services\Shippr\ParcelUnits::getUnits(), null, ['class' => 'form-control']) }}
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group row {{ $errors->has('parcel.weight') ? 'has-danger' : null }}">
                        <label class="form-control-label col-sm-3">Weight</label>
                        <div class="col-sm-3">
                            {{ Form::number('parcel[weight]', null, ['class' => 'form-control numeric float', 'numeric']) }}
                        </div>
                        <div class="col-sm-3">
                            {{ Form::select('parcel[weight_uom]', \Kabooodle\Services\Shippr\WeightUnits::getUnits(), (old('parcel[weight_uom]', 'oz')), ['class' => 'form-control'])  }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box white">
                <div class="box-header">
                    <h4>Recipient Address</h4>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <div class="form-group row {{ $errors->has('to.name') ? 'has-danger' : null }}">
                        <label class="form-control-label col-sm-3">Recipient Name</label>
                        <div class="col-sm-4">
                            {{ Form::text('to[name]', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    @include('profile.partials._addressform', ['_key' => 'to', '_from' => null])
                    <div class="form-group row {{ $errors->has('to.email') ? 'has-danger' : null }}" >
                        <label class="form-control-label col-sm-3">Email</label>
                        <div class="col-sm-4">
                            {{ Form::email('to[email]', null, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="box white">
                <div class="box-header">
                    <h4>Sender Address</h4>
                    <p class="text-muted m-b-0">This address is used on the return label.</p>
                </div>
                <div class="box-divider"></div>
                <div class="box-body">
                    <div class="form-group row {{ $errors->has('from.name') ? 'has-danger' : null }}">
                        <label class="form-control-label col-sm-3">Sender Name</label>
                        <div class="col-sm-4">
                            {{ Form::text('from[name]', user()->name, ['class' => 'form-control']) }}
                        </div>
                    </div>
                    @include('profile.partials._addressform', ['_key' => 'from','_from' => user()->shipFromAddress])
                    <div class="form-group row {{ $errors->has('from.email') ? 'has-danger' : null }}">
                        <label class="form-control-label col-sm-3">Email</label>
                        <div class="col-sm-4">
                            {{ Form::email('from[email]', user()->email, ['class' => 'form-control']) }}
                        </div>
                    </div>
                </div>
            </div>

            {{--<div class="box white">--}}
            {{--<div class="box-body">--}}
            <div class="form-group row m-b-0">
                <div class="col-sm-9 col-sm-offset-3">
                    <button class="btn primary">Contine to pricing</button>
                </div>
            </div>
            {{--</div>--}}
            {{--</div>--}}

            {{ Form::close() }}
        </div>
    </div>


    @push('footer-scripts')
    <script>
        const claims_endpoint = "{{ apiRoute('claims.index') }}"
        const packaging_data = JSON.parse('{!! getParcelListByCarrier(true)->toJson() !!}');
    </script>
    <script src="/assets/js/shipping-create.js"></script>
    @endpush

@endsection