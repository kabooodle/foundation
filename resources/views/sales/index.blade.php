@extends('layouts.full', ['contentId' => 'sales_index'])

@section('body-menu')
    <div class="pull-left">
        <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter Sales</button>
    </div>

@endsection


@section('body-content')

    <div class="navbar-side p-a " id="navbarSide">
        <div class="box ">
            <div class="box-body clearfix">
                <form method="GET" action="{{ route('merchant.sales.index') }}">
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Style</label>
                        <div class="col-sm-9">
                            {{ Form::select('style[]', llrStyles()->pluck('name', 'id'), isset($filters['style']) ? array_values($filters['style']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Size</label>
                        <div class="col-sm-9">
                            {{ Form::select('size[]', llrSizes()->pluck('name', 'id'), isset($filters['size']) ? array_values($filters['size']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Status</label>
                        <div class="col-sm-9">
                            {{ Form::select('statii[]', salesStatii(), isset($filters['statii']) ? array_values($filters['statii']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Categories</label>
                        <div class="col-sm-9">
                            {{ Form::select('categories[]', (categoriesInUse() ? categoriesInUse()->pluck('tag_name', 'id') : []), isset($filters['categories']) ? $filters['categories'] :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Price Range</label>
                        <div class="col-sm-9">
                            <input
                                    name="price_range"
                                    id="input_price_range"
                                    data-slider-id="price_range"
                                    type="text"
                                    data-slider-min="0"
                                    data-slider-max="400"
                                    data-slider-step="1"
                                    data-slider-value="{{isset($filters['price_range']) ? $filters['price_range'] : 400}}"/>
                            <span id="ex6SliderVal" style="margin-left:2px" class=" text-sm">$0 to ${{ isset($filters['price_range']) ? $filters['price_range'] : 400}}</span>
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
                        <label class=" form-control-label col-sm-3 text-sm">Claimer</label>
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

    <div class="box">
        <div class="box-header">
            <h4>Accepted Sales</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col" data-tablesaw-sortable-col data-tablesaw-priority="persist"><input type="checkbox" id="checkAll"></th>
                    <th scope="col" data-tablesaw-sortable-col>Item</th>
                    <th scope="col" data-tablesaw-sortable-col>Price</th>
                    <th scope="col" data-tablesaw-sortable-col>Accepted on</th>
                    <th scope="col" data-tablesaw-sortable-col>Claimer</th>
                    <th scope="col" data-tablesaw-sortable-col>Shipping Status</th>
                    <th scope="col" data-tablesaw-sortable-col></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sales as $sale)
                    <tr>
                    @include('sales.partials._salesrow', ['sale' => $sale])
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="clearfix">
        <div class="pull-right">
            {{ $sales->links() }}
        </div>
    </div>

@endsection


@push('footer-scripts')
<script src="{{  staticAsset('/assets/js/sales-management.js') }}"></script>
<script>
    $(function(){


        $("#input_price_range").slider();

        $("#input_price_range").on("slide", function(slideEvt) {
            $("#ex6SliderVal").text('$0 to $'+slideEvt.value);
        });

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
            items: selectedOptions,
            plugins: ['remove_button'],
            create: false,
            render: {
                option: function(item, escape) {
                    return '<div><span class="title"><span class="name">' + escape(item.text) + '</span></span></div>';
                }
            },
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: '{{ apiRoute('sales.filter') }}',
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
            $('input:checkbox.shipment_checkbox').not(this).prop('checked', this.checked);
        });
    });
</script>
@endpush