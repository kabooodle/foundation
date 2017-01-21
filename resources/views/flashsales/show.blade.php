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
                        <li
                                class="b-b style-list-item">
                            <a  @click="toggleStyleListItem(0, $event)">
                            <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
                            <i class="nav-label"><b class="label b label-sm no-bg text-muted ">1</b></i>
                            <span class="nav-text">Styles</span>
                            </a>
                        </li>
                        <li
                                class="b-b style-list-item">
                            <a  @click="toggleStyleListItem(1, $event)">
                            <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
                            <i class="nav-label"><b class="label b label-sm no-bg text-muted ">1</b></i>
                            <span class="nav-text">Sizes</span>
                            </a>
                        </li>
                        <li
                                class="b-b style-list-item">
                            <a  @click="toggleStyleListItem(2, $event)">
                            <span class="nav-caret text-muted text-xs"><i class="fa fa-caret-down"></i></span>
                            <i class="nav-label"><b class="label b label-sm no-bg text-muted ">1</b></i>
                            <span class="nav-text">Sellers</span>
                            </a>
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
                        fetch_endpoint="{{ apiRoute('listings.show', [$flashsale->listing->uuid]) }}"
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