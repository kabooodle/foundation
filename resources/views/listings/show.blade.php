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
            <h4>{!! $listing->name !!} - {{ $listing->humanize($listing->scheduled_for) }} </h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col"><input type="checkbox"></th>
                    <th scope="col">Album</th>
                    <th scope="col">Item</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Deletes</th>
                    <th scope="col">Gross</th>
                    <th scope="col">Status</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listing->items as $item)
                <tr>
                    <td><input type="checkbox"></td>
                    <td>Joey (SM)</td>
                    <td>    <div class="avatar-thumbnail-container">
                            <div class="avatar-thumbnail _32">
                                @if($item->inventoryItem->firstImage())
                                    <img src="{{ $item->inventoryItem->firstImage()->location }}">
                                @endif
                            </div>
                            <span>{{ $item->inventoryItem->name_with_variant }}</span>
                        </div></td>
                    <td>{{ $item->sales->count() }}</td>
                    <td>{{ $item->pendingSales->count() }}</td>
                    <td>{{ $item->rejectedSales->count() }}</td>
                    <td>$0.00</td>
                    <td>{!! $item->present()->getStatus() !!}</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="/listings/1/show" class="btn btn-xs white">Delete</a>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection