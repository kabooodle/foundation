@extends('layouts.full', ['contentId' => 'flashsale-page'])

@section('body-menu')
    {{--@include('flashsales.partials._bodymenu')--}}
@endsection


@section('body-content')
    <div class="row">
        <div class="col-md-3">
            <div class="navside white r box-shadow-z0 m-b">
                <div class="nav-border b-primary p-b-sm">
                    <ul class="nav">
                        <li class="nav-header hidden-folded"><span class="text-xs text-muted">Search Filters</span></li>
                        @foreach($categories as $categoryName => $sizes)
                            <li
                                    data-style-id="{{ $styleId = $sizes->first()->first()->style_id }}"
                                    class="b-b style-list-item">
                                <a  @click="toggleFilterParent('styles', {{ $styleId }}, $event)">
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
                                            <a @click="toggleFilterChild('sizes', {{ $size->first()->size_id }}, $event)" ><span class="nav-text">{{ $sizeName }}</span></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                        <li
                                data-style-id=""
                                class="b-b style-list-item">
                            <a @click="toggleFilterParent('sellers',null, $event)">
                            <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
                            <i class="nav-label"><b class="label b label-sm no-bg text-muted ">{{ $sellersCategories->count() }}</b></i>
                            <span class="nav-text">Sellers</span>
                            </a>
                            <ul class="nav-sub">
                                @foreach($sellersCategories as $seller)
                                    <li class="size-list-item"
                                    >
                                        <i class="nav-label m-r-1 m-l-0 "><b class="label label-sm no-bg text-muted ">{{ $seller->items_count }}</b></i>
                                        <a @click="toggleFilterChild('sellers', {{ $seller->id }}, $event)" ><span class="nav-text">{{ $seller->username }}</span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>

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
            @include('flashsales.partials._flashsaleheader', ['item' => $flashsale])

            <div class="row content">
                <listing-cards
                        fetch_endpoint="{{ $flashsale->listing ? apiRoute('listings.show', [$flashsale->listing->uuid]) : null }}"
                        watch_endpoint="{{ apiRoute('listings.listingitems.watchers.store', ['::1::','::2::']) }}"
                        show_endpoint="{{ route('listingitems.show', ['::1::']) }}"
                ></listing-cards>
            </div>
        </div>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsale-items-page.js') }}"></script>
@endpush