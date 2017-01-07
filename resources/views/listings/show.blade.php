@extends('layouts.full', ['contentId' => 'listing-page'])

@section('body-menu')
    <button class="btn white btn-sm">Filter</button>
@endsection

@section('body-content')

    <div class="box white r">
        <div class="box-header clearfix">
            <h4 class="pull-left"> @include('listings._listingtype', ['_type' => $listing->type]) {{ $listing->sale_name }}</h4>
            <div class="pull-right text-muted text-sm">
                <p class="m-0 m-b-0 p-o ">Items can be claimed Jan 1 through Jan 8</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <v-card
                    avatar_size="32"
                    extra_class="no-border b-0 b-b-0 "
                    already_following="{{ $listing->owner->is_following ? 'true' : 'false'}}"
                    follow_endpoint="{{ apiRoute('user.followers.store', [$listing->owner->id]) }}"
                    able_type="{{ get_class($listing->owner) }}"
                    able_id="{{ $listing->owner->id }}"
                    :user="{{ $listing->owner }}"
                    message_endpoint="{{ apiRoute('messenger.store') }}"
            ></v-card>
            <div class="list-group m-b">
                @foreach($categories as $category)
                <a href="" data-style-id="{{ $category->id }}" class="list-group-item">
                   {{ $category->name }}
                    <span class="pull-right label">{{ $category->count_per_style }}</span>
                </a>
                @endforeach
            </div>
        </div>
        <div class="col-md-9">
            <div class="row">
                @foreach($items as $item)
                    <div class="col-md-3">
                        <div class="box p-a-xs p-b-0">
                                <span class="item avatar_container block-center center _128h avatar-thumbnail">
                                    <div class="item-overlay active p-a-xs" style="z-index:999">
                                            <followable
                                                    able_name="watchable"
                                                    able_type="{{ get_class($item) }}"
                                                    able_id="{{ $item->id }}"
                                                    unfollow_text="Watching"
                                                    follow_text="Watch"
                                                    btn_size_class="pull-right label dark-white text-color btn-xs"
                                                    already_following="{{ $item->is_watched ? 'true' : 'false' }}"
                                                    endpoint="{{ apiRoute('listings.listingitems.watchers.store', [$item->listing_id, $item->id]) }}"
                                            ></followable>
                                </div>
                                    <a href="{{ route('listingitems.show', [$item->obfuscateIdToString()]) }}">
                                        <img src="{{ $item->inventoryItem->firstImage() ? $item->inventoryItem->firstImage()->location : 'https://placekitten.com/g/32/20'}}" class="img-responsive">
                                    </a>
                            </span>
                            <div class="p-a-sm p-b-0">
                                <div class="clearfix">
                                    <h6 class="m-b-0"><a class="" href="{{ route('listingitems.show', [$item->obfuscateIdToString()]) }}">{!! $item->inventoryItem->name !!}</a></h6>
                                    <div class="m-b-0 text-sm clearfix">
                                        <div class="pull-left">
                                            <div class="block">
                                                <span class="text-muted">Size:</span> <span class="">{!! $item->inventoryItem->styleSize->name !!}</span>
                                            </div>

                                        </div>
                                        <div class="pull-right" style="text-align: right">
                                            <span class="text-muted ">Qty:</span> <span class="">{{ $item->inventoryItem->getAvailableQuantity() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/listing-index.js') }}"></script>
@endpush
