@extends('layouts.full')



@section('body-menu')
    <div class="pull-left">
        <a class="btn btn-sm white" href="{{ route('listings.index') }}">
            Filter Listings
        </a>
    </div>

@endsection


@section('body-content')
    <div class="box">
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Scheduled For</th>
                    <th scope="col">Display Link</th>
                    <th scope="col">Albums</th>
                    <th scope="col">Items</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Gross</th>
                    <th scope="col">Status</th>
                </tr>
                </thead>
                <tbody>
                @include('listings.partials._indexrow', ['listing' => $listing, '_excludeActionCol' => true])
                </tbody>
            </table>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h4>Edit Listing</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div class="form-group row">
                <label class="control-label col-sm-3">List on a specific date and time</label>
                <div class="col-sm-4">
                    <input type="" class="form-control">
                </div>
            </div>
            <div class="form-group row">
                <label class="control-label col-sm-3">Include Link in description</label>
                <div class="col-sm-3">
                    <input type="checkbox" class="form-control">
                </div>
            </div>
        </div>
    </div>

@endsection