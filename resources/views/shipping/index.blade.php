@extends('layouts.full', ['contentId' => 'shipping_index'])


@section('body-menu')
        <div class="pull-left">
            <button type="button" id="btn-toggle-filters" class="btn btn-sm white">Filter Transactions</button>
        </div>

    {{--<div class="pull-right">--}}
        {{--<a href="{{ route('shipping.create') }}" class="btn btn-sm white">Create new shipment</a>--}}
    {{--</div>--}}
@endsection


@section('body-content')

    <div class="navbar-side p-a " id="navbarSide">
        <div class="box ">
            <div class="box-body clearfix">
                <form>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-4 text-sm">Status</label>
                        <div class="col-sm-8">
                            {{ Form::select('status[]', \Kabooodle\Models\ShippingTransactions::SHIPPING_STATII, null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-4 text-sm">Dates</label>
                        <div class="col-sm-8">
                            {{ Form::select('date_range', \Kabooodle\Models\ShippingTransactions::SHIPPING_STATII, null, ['class' => 'form-control ', 'data-toggle' => 'multiselect']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-4 text-sm">Recipients</label>
                        <div class="col-sm-8">
                            {{ Form::text('recipients[]', null, ['class' => '', 'id' => 'input-recipients']) }}
                        </div>
                    </div>
                    <div class="form-group row p-b-0 m-b-0">

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h4>Shipping Transactions</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table class="table table-condensed table-as-list white">
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
                @foreach($shipments as $shipment)
                    <tr>
                        <td><input type="checkbox" class="shipment_checkbox"></td>
                        <td>{{ $shipment->shipment->claimer->name }}</td>
                        <td>{{ $shipment->shipment->claims->count() }}</td>
                        <td>${{ $shipment->rate_amount }}</td>
                        <td><time datetime="{{ $shipment->createdAtHuman() }}">{{ $shipment->createdAtHumanNoTime() }} <i data-toggle="tooltip" title="{{ $shipment->createdAtHuman() }}" data-placement="top" class="fa fa-clock-o" aria-hidden="true"></i></time></td>
                        <td><a class="text-primary" href="{{ $shipment->tracking_url_provider }}" target="_blank" >{{ $shipment->tracking_number }}</a> <i class="fa fa-external-link" aria-hidden="true"></i></td>
                        <td>{!! $shipment->present()->getStatus()  !!}</td>
                        <td>
                            <div class="pull-right">
                                <a href="{{ route('shipping.transactions.show', [$shipment->shipping_shipments_uuid, $shipment->uuid]) }}"  class="btn btn-xs white">View</a>
                                <a target="_blank" href="{{ route('shipping.transactions.label.show', [$shipment->shipping_shipments_uuid, $shipment->uuid])}}" class="btn btn-xs white">Shipping Label</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection


@push('footer-scripts')

<script>

    $(function(){
        $('#btn-toggle-filters').click(function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal')
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

        $('#input-recipients').selectize({
            valueField: 'title',
            labelField: 'title',
            searchField: 'title',
            options: [],
            create: false,
            render: {
                option: function(item, escape) {
                    var actors = [];
                    for (var i = 0, n = item.abridged_cast.length; i < n; i++) {
                        actors.push('<span>' + escape(item.abridged_cast[i].name) + '</span>');
                    }

                    return '<div>' +
                            '<img src="' + escape(item.posters.thumbnail) + '" alt="">' +
                            '<span class="title">' +
                            '<span class="name">' + escape(item.title) + '</span>' +
                            '</span>' +
                            '<span class="description">' + escape(item.synopsis || 'No synopsis available at this time.') + '</span>' +
                            '<span class="actors">' + (actors.length ? 'Starring ' + actors.join(', ') : 'Actors unavailable') + '</span>' +
                            '</div>';
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
                    error: function(e,x) {
                        console.log(e,x);
                        callback();
                    },
                    success: function(res) {
                        callback(res.movies);
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