@extends('layouts.full', ['contentId' => 'shipping_index'])

@if($shipments->count() <> 0)
@section('body-menu')
    <div class="pull-left">
        <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter Transactions</button>
    </div>

    <div class="pull-right">
        <a href="{{ route('merchant.shipping.create') }}" class="btn btn-sm white">Create new shipment</a>
    </div>
@endsection
@endif

@section('body-content')

    <div class="navbar-side p-a " id="navbarSide">
        <div class="box ">
            <div class="box-body clearfix">
                <form method="GET" action="{{ route('merchant.shipping.index') }}">
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Status</label>
                        <div class="col-sm-9">
                            {{ Form::select('status[]', shippingStatii(), isset($filters['statii']) ? array_values($filters['statii']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Dates</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group" id="datepicker">
                                <input type="text" id="datetimepicker1" class="input-sm form-control" name="startdate" value="{{ $filters['startdate'] or null }}"/>
                                <span class="input-group-addon" style="font-size: .75rem; border-left: none; border-right: none; padding-left: .4rem; padding-right: .4rem;">to</span>
                                <input type="text"  id="datetimepicker2" class="input-sm form-control" name="enddate" value="{{ $filters['enddate'] or null }}"/>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Recipients</label>
                        <div class="col-sm-9">
                            {{ Form::select('purchasers[]', isset($filters['purchasers']) && $filters['purchasers'] <> false ? $filters['purchasers'] : [],  $filters['purchasers'] or null, ['class' => '', 'id' => 'input-recipients', 'placeholder' => 'Begin typing names', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row p-b-0 m-b-0">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn-sm btn primary">Go</button>
                            <button type="button" class="btn white btn-sm btn-toggle-reset" >Reset</button>
                            <a  href="#" class=" m-l-sm text-sm btn-toggle-filters" >Close</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @if($shipments->count() == 0)

        <div class="onboard-card onboard_wrapper onboard-shipping">
            <div class="onboard-body text-center">
                <h1 class="onboard-card-title">
                   Ship inventory directly through USPS
                </h1>
                <h2 class="onboard-card-sub-title text-center m-b-3">
                    Easily generate a USPS shipping label, for a single item, or multiple items at once.
                    <br>
                    Address information is filled in automatically based on the recipients shipping profile.
                    <br>
                    As the tracking information is updated, we update you and the recipient.
                </h2>
                <button class="btn btn-lg btn-grn ">Create your first shipment!</button>
            </div>
        </div>

    @else

    <div class="box">
        <div class="box-header">
            <h4>Shipping Transactions</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Recipient</th>
                        <th>Items</th>
                        <th>Cost</th>
                        <th>Date</th>
                        <th>Tracking</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @if($shipments->count() > 0)
                    @foreach($shipments as $shipment)
                        <tr>
                            <td><input type="checkbox" class="shipment_checkbox"></td>
                            <td>{{ $shipment->shipment->claimer->username }}</td>
                            <td>{{ $shipment->shipment->claims->count() }}</td>
                            <td>${{ $shipment->rate_amount }}</td>
                            <td><time datetime="{{ $shipment->createdAtHuman() }}">{{ $shipment->createdAtHumanNoTime() }} <i data-toggle="tooltip" title="{{ $shipment->createdAtHuman() }}" data-placement="top" class="fa fa-clock-o" aria-hidden="true"></i></time></td>
                            <td><a class="text-primary" href="{{ $shipment->tracking_url_provider }}"  id="link-text-{{ $shipment->id }}" target="_blank" >{{ $shipment->tracking_number }}</a>
                                <a href="javascript:;" data-animation="false" data-clipboard-target="#link-text-{{ $shipment->id }}"><i class="fa fa-clipboard" aria-hidden="true"></i></a></td>
                            <td>{!! $shipment->present()->getStatus()  !!}</td>
                            <td>
                                <div class="pull-right">
                                    <a href="{{ route('merchant.shipping.transactions.show', [$shipment->shipping_shipments_uuid, $shipment->uuid]) }}"  class="btn btn-xs white">View</a>
                                    <a target="_blank" href="{{ route('merchant.shipping.transactions.label.show', [$shipment->shipping_shipments_uuid, $shipment->uuid])}}" class="btn btn-xs white">Shipping Label</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection


@push('footer-scripts')

<script>

    $(function(){

        let dateTimePickerSettings = {
            format: "MM/DD/YYYY",
            maxDate: moment(new Date()).add(1,'hours'),
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            },
            useCurrent: false
        };

        let dateTimePickerEl1 = $('#datetimepicker1');
        let dateTimePickerEl2 = $('#datetimepicker2');

        dateTimePickerEl1.datetimepicker(dateTimePickerSettings);
        dateTimePickerEl2.datetimepicker(dateTimePickerSettings);

        dateTimePickerEl1.on("dp.change", function (e) {
            dateTimePickerEl2.data("DateTimePicker").minDate(e.date);
        });
        dateTimePickerEl2.on("dp.change", function (e) {
            dateTimePickerEl1.data("DateTimePicker").maxDate(e.date);
        });

        $('.btn-toggle-filters').click(function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal')
        });

        $('.btn-toggle-reset').click(function(event){
            $('#navbarSide')
                    .find('input, select').val(null).trigger('change').find('option').prop('selected', false);
        });

        $('table tbody tr').not('.btn, a').click(function(event){
            let ignore = ['input', 'a', 'button', 'textarea', 'label'];
            let clicked = event.target.nodeName.toLowerCase();
            let input =  $(this).find('input:checkbox');
            if($.inArray(clicked, ignore) > -1) {
                return;
            }

           if(input.is(':checked')){
               input.prop('checked', false).trigger('change');
           } else {
               input.prop('checked', true).trigger('change');
           }
        });

        let selectedOptions = [];
        let selectedOptionsJson = JSON.parse('{!! json_encode($filters['purchasers']) !!}');
        if(_.size(selectedOptionsJson) >0) {
            selectedOptions = _.map(selectedOptionsJson, function(value, index) {
                return index;
            });
        }

        $('#input-recipients').selectize({
            valueField: 'value',
            labelField: 'text',
            searchField: 'text',
            delimiter: ',',
            persist: false,
            plugins: ['remove_button'],
            items: selectedOptions,
            create: false,
            render: {
                option: function(item, escape) {
                    return '<div><span class="title"><span class="name">' + escape(item.text) + '</span></span></div>';
                }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '{{ apiRoute('shipping.filter') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        q: query,
                        method: 'recipients'
                    },
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res.data);
                    }
                });
            }
        });

        $('#checkAll').click(function(event){
            $('input:checkbox').not(this).prop('checked', this.checked);
        });
    });
</script>

@endpush