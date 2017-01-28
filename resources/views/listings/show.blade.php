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
                        already_following="{{ $listing->owner->is_following ? 'true' : 'false'}}"
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
                        @foreach($categories as $categoryName => $sizes)
                        <li
                                data-style-id="{{ $styleId = $sizes->first()->first()->style_id }}"
                                class="b-b style-list-item">
                            <a  @click="toggleStyleListItem({{ $styleId }}, $event)">
                                <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
                                <i class="nav-label"><b class="label b label-sm no-bg text-muted ">{{ $sizes->flatten()->count() }}</b></i>
                                <span class="nav-text">{{ $categoryName }}</span>
                            </a>
                            <ul class="nav-sub">
                                @foreach($sizes as $sizeName => $size)
                                <li class="size-list-item"
                                    data-style-id="{{ $styleId }}"
                                    data-size-id="{{ $size->first()->size_id }}"
                                >
                                    <i class="nav-label m-r-1 m-l-0 "><b class="label label-sm no-bg text-muted ">{{ $size->flatten()->count() }}</b></i>
                                    <a @click="toggleSizeListItem({{ $size->first()->size_id }}, {{ $styleId }}, $event)" ><span class="nav-text">{{ $sizeName }}</span></a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @endforeach
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
