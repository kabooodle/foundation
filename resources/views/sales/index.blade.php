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
                <form method="GET" action="{{ route('shipping.index') }}">
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Style</label>
                        <div class="col-sm-9">
                            {{ Form::select('style[]', llrStyles()->pluck('name', 'id'), isset($filters['statii']) ? array_values($filters['statii']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Size</label>
                        <div class="col-sm-9">
                            {{ Form::select('size[]', llrSizes()->pluck('name', 'id'), isset($filters['statii']) ? array_values($filters['statii']) :  null, ['class' => 'form-control ', 'data-toggle' => 'multiselect', 'multiple']) }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Price</label>
                        <div class="col-sm-9">
                            <input
                                    type="text"
                                    name="somename"
                                    data-provide="slider"
                                    data-slider-ticks="[1, 2, 3]"
                                    data-slider-ticks-labels='["short", "medium", "long"]'
                                    data-slider-min="1"
                                    data-slider-max="3"
                                    data-slider-step="1"
                                    data-slider-value="3"
                                    data-slider-tooltip="hide"
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class=" form-control-label col-sm-3 text-sm">Size</label>
                        <div class="col-sm-9">
                            
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
                            {{ Form::text('recipients[]', null, ['class' => '', 'id' => 'input-recipients', 'placeholder' => 'Enter names']) }}
                        </div>
                    </div>
                    <div class="form-group row p-b-0 m-b-0">
                        <div class="col-sm-9 col-sm-offset-3">
                            <button type="submit" class="btn-sm btn primary">Go</button>
                            <button type="button" class="btn white btn-sm btn-toggle-filters" >Close</button>
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
            <table class="table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th><input type="checkbox" id="checkAll"></th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Accepted on</th>
                    <th>Claimer</th>
                    <th>Shipping Status</th>
                    <th></th>
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
<script src="/assets/js/sales-management.js"></script>
<script>
    $(function(){
        $('.btn-toggle-filters').click(function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal')
        });
    });
</script>
@endpush