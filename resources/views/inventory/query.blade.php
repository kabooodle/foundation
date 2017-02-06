@extends('layouts.body_w_leftnav')

@section('body-menu')

    <div class="pull-left">
        <div class="btn-toolbar center-block text-center">
            <div class="btn-group dropdown">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Filter</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">By Name</a>
                    <a class="dropdown-item" href="">By Quantity</a>
                </div>
            </div>

            <div class="btn-group dropdown ">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Bulk</span>
                    <span class="caret"></span>
                </button>
                <div class="dropdown-menu text-left text-sm">
                    <a class="dropdown-item" href="">Delete</a>
                </div>
            </div>

            <a style="display: none" id="btn_add_selected" data-toggle="modal" data-target="#m-md"
               data-backdrop="static" data-keyboard="false" href="#" class="text-white  btn btn-sm warning">Add Selected
                Items to Sale</a>

        </div>
    </div>


    <div class="pull-right">
        <a href="{{ route('shop.inventory.index', [webUser()->username]) }}" class="btn btn-sm white">Manage</a>
        <a href="{{ route('shop.inventory.create', [webUser()->username]) }}" class="btn btn-sm white">Add Items</a>
    </div>

@endsection


@section('body-content')

    <table class="table table-condensed table-as-list white" style="width: 100%">
        <thead>
        <tr>
            <th>
                <div style="padding-left: 12px !important;"><input id="select_all" type="checkbox" class=""></div>
            </th>
            <th class="text-muted">Details</th>
            <th class="text-muted p-l-0 m-l-0">Price</th>
            <th class="text-muted p-l-0 m-l-0">Claims</th>
            <th class="text-muted p-l-0 m-l-0">*Available Qty</th>
            <th></th>
        </tr>
        </thead>
    </table>

    @push('footer-scripts')

    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.12/datatables.min.js"></script>

    <script>
        $(function(){
            var ownedtable = $('table.table-as-list').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('shop.inventory.query', [webUser()->username]) }}",
                columns: [
                    { data: 'id', name: 'id' },
                    {data: 'name', name: 'name'},
                    {data: 'price_usd', name: 'price_usd'},
                    {data: 'claims', name: 'claims'},
                    {data: 'initial_qty', name: 'initial_qty'}
                ]
            });
        });
    </script>

    @endpush
@endsection