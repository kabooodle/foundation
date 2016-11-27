@extends('layouts.full')



@section('body-menu')

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
                        <th scope="col">Group</th>
                        <th scope="col">Listing Date</th>
                        <th scope="col">Albums</th>
                        <th scope="col">Items</th>
                        <th scope="col">Sales</th>
                        <th scope="col">Views</th>
                        <th scope="col">Status</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($listings as $listing)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{!! $listing->name !!}</td>
                        <td>Pipers Group</td>
                        <td>{{ $listing->humanize($listing->scheduled_for) }}</td>
                        <td>{{ $listing->albumsCount() }}</td>
                        <td>{{ $listing->items->count() }}</td>
                        <td>N/A</td>
                        <td>N/A</td>
                        <td>{!! $listing->present()->getStatus() !!}</td>
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