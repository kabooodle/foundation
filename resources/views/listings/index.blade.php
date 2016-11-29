@extends('layouts.full')



@section('body-menu')
    <div class="pull-left">
        <button class="btn btn-sm white">Filter Listings</button>
    </div>

@endsection


@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Listings</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                    <tr>
                        <th scope="col"><input type="checkbox"></th>
                        <th scope="col">Name</th>
                        <th scope="col">Listing Date</th>
                        <th scope="col">Albums</th>
                        <th scope="col">Items</th>
                        <th scope="col">Sales</th>
                        <th scope="col">Pending</th>
                        <th scope="col">Gross</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($listings as $listing)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>Pipers Group @include('listings._listingtype', ['_type' => $listing->type])</td>
                        <td>{{ humanizeDateTime($listing->scheduled_for) }}</td>
                        <td><a href="{{ route('listings.show', [$listing->uuid]) }}" class="text-primary">{{ $listing->albums_count }}</a></td>
                        <td><a class="text-primary" href="{{ route('listings.group.show', [ $listing->uuid, $listing->type, ($listing->type == 'facebook' ? $listing->fb_album_id : $listing->flashsale_id) ]) }}">{{ $listing->items_count }}</a></td>
                        <td>{{ $listing->accepted_sales_count }}</td>
                        <td>{{ $listing->pending_sales_count }}</td>
                        <td>${{ $listing->gross }}</td>
                        <td>{!! listingStatusHtml($listing->status) !!}</td>
                        <td>
                            <div class="pull-md-right">
                                <a href="{{ route('listings.show', [$listing->uuid]) }}" class="btn btn-xs white">View</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection