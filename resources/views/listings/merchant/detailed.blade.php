@extends('layouts.full')



@section('body-menu')
    <div class="pull-left">
        <a class="btn btn-sm white" href="{{ route('merchant.listings.index') }}">
            Filter Listings
        </a>
    </div>

@endsection


@section('body-content')

    @include('listings.partials._listingbox', ['listing' => $listing])

    <div class="box">
        <div class="box-header">
            <h4>Listed Inventory Items</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                <thead>
                <tr>
                    <th scope="col"><input type="checkbox"></th>
                    <th scope="col">Item</th>
                    <th scope="col">Sales</th>
                    <th scope="col">Pending</th>
                    <th scope="col">Views</th>
                    <th scope="col">Watchers</th>
                    <th scope="col">Gross</th>
                    <th scope="col">Status</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($listing->listingItems as $item)
                <tr>
                    <td><input type="checkbox"></td>
                    <td> <div class="avatar-thumbnail-container">
                            <div class="avatar-thumbnail _32">
                                <img src="{{ $item->inventoryItem->cover_photo->location }}">
                            </div>
                            <span>{{ $item->inventoryItem->name_with_variant }}</span>
                        </div></td>
                    <td>{{ $item->sales->count() }}</td>
                    <td>{{ $item->pendingSales->count() }}</td>
                    <td>{{ $item->pageViews->count() }}</td>
                    <td>{{ $item->watchers->count() }}</td>
                    <td>${{ $item->sales->sum('price') }}</td>
                    <td>{!! $item->present()->getStatus()  !!}</td>
                    <td><a class="btn btn-xs white" href="{{ route('listingitems.show', [$item->obfuscateIdToString()]) }}">View item listing</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection