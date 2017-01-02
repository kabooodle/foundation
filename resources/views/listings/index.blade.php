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
                        <th scope="col">Scheduled For</th>
                        <th scope="col">Display Link</th>
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
                    @include('listings.partials._indexrow', ['listing' => $listing])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection


@push('footer-scripts')

<script>
    var channel = KABOOODLE_APP.pusher.subscribe('private.'+KABOOODLE_APP.env+'.listings.'+KABOOODLE_APP.currentUser.id);
    channel.bind('listing:updated', function(data) {
        let target_row = $('tr[data-id="'+data.id+'"]');
        if (target_row.length) {
            target_row.replaceWith(data.html);
        }
    });
</script>

@endpush