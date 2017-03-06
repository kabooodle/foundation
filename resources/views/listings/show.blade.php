@extends('layouts.full', ['contentId' => 'listing-page'])

{{--@section('body-menu')--}}
    {{--<button class="btn white btn-sm">Share</button>--}}
{{--@endsection--}}

@section('body-content')

    <div class="box white r">
        <div class="box-header clearfix">
            <h4 class="pull-left"> @include('listings._listingtype', ['_type' => $listing->type]) {{ $listing->sale_name }}</h4>
            <div class="pull-right text-muted text-sm">
                <p class="m-0 m-b-0 p-o ">Items can be claimed {{ $listing->claimable_range }}</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="box white m-b-2">
                <v-card
                        avatar_size="32"
                        extra_class="no-border b-0 b-b-0"
                        :already_following="{{ $listing->owner->is_following ? 1 : 0 }}"
                        follow_endpoint="{{ apiRoute('user.followers.store', [$listing->owner->id]) }}"
                        able_type="{{ get_class($listing->owner) }}"
                        able_id="{{ $listing->owner->id }}"
                        :user="{{ $listing->owner }}"
                        message_endpoint="{{ apiRoute('messenger.store') }}"
                ></v-card>
            </div>

            <div class="navside white r box-shadow-z0 m-b">
                <div class="nav-border b-primary p-b-sm">
                    <ul class="nav">
                        <li class="nav-header hidden-folded"><span class="text-xs text-muted">Search Filters</span></li>
                        @include('listings.partials._categories_filter_options')
                    </ul>
                    <div class="p-t-1 p-r-sm p-l-sm">
                        <button
                                class="btn btn-block white"
                                :class="filtering ? 'disabled' : null"
                                :disabled="filtering ? true : false"
                                @click="filterListing"
                        >Filter <spinny v-if="filtering"></spinny></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div class="row content">
                <listing-cards
                        fetch_endpoint="{{ apiRoute('listings.show', [$listing->uuid]) }}"
                        watch_endpoint="{{ apiRoute('listings.listingitems.watchers.store', ['::1::','::2::']) }}"
                        show_endpoint="{{ route('listingitems.show', ['::1::']) }}"
                ></listing-cards>
            </div>
        </div>
    </div>
@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listing-index.js') }}"></script>
@endpush
