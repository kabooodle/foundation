@extends('layouts.full')


@section('body-content')
    @include('listings.partials._listingbox', ['listing' => $listing])

    <div class="box">
        <div class="box-header">
            <h4>Albums</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col">Album Name</th>
                    <th scope="col">Items</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Views</th>
                    <th scope="col">Gross</th>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listings as $item)
                <tr>
                    <td>{{ $item->type == 'facebook' ? $item->fb_name : $item->flashsale_name }}</td>
                    <td>{{ $item->items_count }}</td>
                    <td>{{ $item->accepted_sales_count }}</td>
                    <td>{{ $item->pending_sales_count }}</td>
                    <td>{{ $item->pageviews_count }}</td>
                    <td>${{ $item->gross }}</td>
                    <td>
                        <div class="pull-md-right">
                            <a href="{{ route('merchant.listings.group.show', [ $item->uuid, $item->type, ($item->type == 'facebook' ? $item->fb_album_id : $item->flashsale_id) ]) }}" class="btn btn-xs white">Manage Items</a>
                        </div>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection