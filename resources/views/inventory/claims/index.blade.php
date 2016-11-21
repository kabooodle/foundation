@extends('layouts.full', ['contentId' => 'claims_index'])

@section('body-menu')

    <div class="btn-toolbar center-block text-center">
        <div class="btn-group dropdown">
            <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                <span class="dropdown-label">Filter</span>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Active</a>
                <a class="dropdown-item" href="">Archived</a>
            </div>
        </div>
    </div>

@endsection


@section('body-content')

    <div class="box">
        <div class="box-body">
            <div id="claims__wrapper">
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr class="  ">
                        <th></th>
                        <th class="text-muted">Item</th>
                        <th class="text-muted p-l-0 m-l-0">Claim Price</th>
                        <th class="text-muted p-l-0 m-l-0">Claimer</th>
                        <th class="text-muted p-l-0 m-l-0">Claimed On</th>
                        <th></th>
                    </tr>
                    </thead>
                        <claims-index :claims="{{ $data->getCollection()->toJson() }}"
                        ></claims-index>
                </table>


            </div>

        </div>
    </div>


    <script src="/assets/js/claims-index.js"></script>


@endsection