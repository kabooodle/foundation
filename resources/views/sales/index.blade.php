@extends('layouts.full', ['contentId' => 'sales_index'])


@section('body-menu')
    <div class="pull-left">
        <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter Sales</button>
    </div>

@endsection


@section('body-content')

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
@endpush