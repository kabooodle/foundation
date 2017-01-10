@extends('layouts.full', ['contentId' => 'watching_items'])

@section('body-content')

    <div class="box">
        <div class="box-header">
            <h4>Items you are watching</h4>
        </div>
        <div class="box-divider"></div>
        <div class="box-body">
            <div id="claims__wrapper">
                <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                    <thead>
                    <tr class="  ">
                        <th class="text-muted">Item</th>
                        <th class="text-muted">Name</th>
                        <th class="text-muted p-l-0 m-l-0">Price</th>
                        <th class="text-muted p-l-0 m-l-0">Seller</th>
                        <th class="text-muted p-l-0 m-l-0">Posted On</th>
                        <th class="text-muted p-l-0 m-l-0">Last Updated</th>
                        <th class="text-muted p-l-0 m-l-0">Status</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($watching)
                        @foreach($watching as $watch)
                            @include('purchases.partials._itemswatched', ['watch' => $watch])
                        @endforeach
                    @else

                    @endif
                    </tbody>
                </table>

            </div>

        </div>
    </div>

@endsection


@push('footer-scripts')

<script src="{{ staticAsset('/assets/js/watching-items.js') }}"></script>
@endpush