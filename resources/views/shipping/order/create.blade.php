@extends('layouts.full', ['contentId' => 'shipping_create'])

@section('body-content')

    <div id="steps_1">
        <div :class="completed_steps.parcel ? 'box-default' : null" class="box no-shadow">
            <div class="box-header">
                <h3 class="m-b-0"><span class="text-muted">Step 1:</span> Shipping Parcel &amp; Recipient Information
                    <template v-if="completed_steps.parcel">
                        <i class="fa text-success fa-check-circle"></i>
                        <button class="btn white btn-xs" type="button" @click.prevent="viewParcelData">Edit</button>
                    </template>
                </h3>
            </div>
        </div>

        <div id="shipping_parcel_form" v-show="! completed_steps.parcel">
            <shipping-parcel-form
                    :parcel_units="{{ json_encode(\Kabooodle\Services\Shippr\ParcelUnits::getUnits())  }}"
                    :parcel_weights="{{ json_encode(\Kabooodle\Services\Shippr\WeightUnits::getUnits())  }}"
                    :packaging_data="{{ getParcelListByCarrier(true)->toJson() }}"
                    :states="{{ json_encode(getStateAbbrevs()) }}"
                    endpoint_submit_parcel="{{ apiRoute('shipping.parcel.store') }}"
                    endpoint_claims="{{ apiRoute('claims.index') }}"
                    :sender_address="{{ webUser()->primaryShipFromAddress ? webUser()->primaryShipFromAddress  : '{}' }}"
            ></shipping-parcel-form>
        </div>
    </div>


    <div id="steps_2" class="hidden" v-show="completed_steps.parcel">
        <div class="box  m-b-0 no-shadow">
            <div class="box-header">
                <h3 class="m-b-0"><span class="text-muted">Step 2:</span> Shipping Rates &amp; Label</h3>
            </div>
        </div>

        <div id="shipping_label_form">
            <div class="row">
                <div class="col-md-12">
                    <div class="box no-shadow white" id="shipping_rates_wrapper">
                        <div class="box-divider"></div>
                        <div class="box-body">
                            <table class="table table-condensed table-as-list white">
                                <thead>
                                <tr>
                                    <th>Carrier</th>
                                    <th>Service Name</th>
                                    <th>*Transit Time</th>
                                    <th>Cost</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="rate in rates">
                                    <td><img
                                                :src="rate.shippoRateObject.provider_image_75"
                                                :alt="rate.provider"
                                                :title="rate.provider"></td>
                                    <td>@{{ rate.shippoRateObject.servicelevel_name }}</td>
                                    <td>@{{ rate.days }} days
                                        <small class="block text-muted">@{{ rate.durationTerms}}</small>
                                    </td>
                                    <td>$@{{ rate.adjustedTotalAmount }}</td>
                                    <td class="pull-right">
                                        <button
                                                type="button"
                                        @click.prevent="purchaseLabel('{{ apiRoute('shipping.label.store') }}', rate.shippoRateObject.object_id, $event)"
                                        class="btn btn-xs white btn-purchase-label-el">
                                        Purchase Label
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="box-footer p-t-0">
                                <small class="font-italic">*Transit time is a non-guaranteed average. The number of days
                                    is the best estimate available. Some carriers guarantee this delivery time.
                                </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('footer-scripts')
<script src="{{staticAsset('/assets/js/shipping-create.js')}}"></script>
@endpush