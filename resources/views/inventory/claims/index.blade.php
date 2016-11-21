@extends('layouts.full', ['contentId' => 'claims_index'])


@section('body-menu')
    <div class="btn-toolbar center-block text-center">
            <div class="btn-group dropdown">
                <button class="btn white btn-sm dropdown-toggle" data-toggle="dropdown">
                    <span class="dropdown-label">Bulk</span>
                    <span class="caret"></span>
                </button>
            <div class="dropdown-menu text-left text-sm">
                <a class="dropdown-item" href="">Accept</a>
                <a class="dropdown-item" href="">Reject</a>
            </div>
        </div>
    </div>
@endsection


@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Pending claims on your inventory</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div id="claims__wrapper">
                <table class="table table-condensed table-as-list white">
                    <thead>
                    <tr class="  ">
                        <th><input type="checkbox" id="checkAll" @click="toggleChecks"></th>
                        <th class="text-muted">Item</th>
                        <th class="text-muted p-l-0 m-l-0">Claim Price</th>
                        <th class="text-muted p-l-0 m-l-0">Claimer</th>
                        <th class="text-muted p-l-0 m-l-0">Claimed On</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody is="claims-index"  :claims="{{ $data->getCollection()->toJson() }}">
                    </tbody>
                </table>

            </div>

        </div>
    </div>

    @include('inventory.claims.partials._actionmodal')
@endsection


@push('footer-scripts')
<script src="/assets/js/claims-index.js"></script>
@endpush