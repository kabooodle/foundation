@extends('layouts.full')



@section('body-menu')
    <div class="pull-left">
        <a class="btn btn-sm white" href="{{ route('listings.index') }}">
            Filter Listings
        </a>
    </div>

@endsection


@section('body-content')
    <div class="row">
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">0 <span class="text-sm">Sales</span></a></h4>
                    <small class="text-muted">0 waiting payment.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">0 <span class="text-sm">Pending</span></a></h4>
                    <small class="text-muted">3 pending approval.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">2 <span class="text-sm">Rejected</span></a></h4>
                    <small class="text-muted">2 rejected claims.</small>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="box p-a">
                <div class="pull-left m-r">
            <span class="w-40 warn text-center rounded">
              <i class="material-icons">shopping_basket</i>
            </span>
                </div>
                <div class="clear">
                    <h4 class="m-a-0 text-md"><a href="">$75 <span class="text-sm">Gross</span></a></h4>
                    <small class="text-muted">$5.00</small>
                </div>
            </div>
        </div>
    </div>

    <div class="box">
        <div class="box-header">
            <h4>{{ $listing->humanize($listing->scheduled_for) }} </h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col"><input type="checkbox"></th>
                    <th scope="col">Name</th>
                    <th scope="col">Items</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Deletes</th>
                    <th scope="col">Views</th>
                    <th scope="col">Gross</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listing->listingsGroupedByItemTypeGrouping() as $item)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->items_count ? : 0}}</td>
                    <td>{{ $item->accepted_sales_count ? : 0}}</td>
                    <td>{{ $item->pending_sales_count ? : 0 }}</td>
                    <td>{{ $item->rejected_sales_count ? : 0}}</td>
                    <td>0</td>
                    <td>${{ ($item->price_sum + $item->accepted_price_sum) ? : 0 }}</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">View</a>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection