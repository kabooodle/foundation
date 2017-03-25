@extends('layouts.full', ['contentId' => 'flashsale-page'])


@push('facebook-tags')
<meta property="og:title" content="{{ $flashsale->name }}" />
<meta property="og:image"  content="{{ $flashsale->coverimage->location }}" />
@endpush

@section('body-menu')

    <div class="pull-right">
        {{--<followable--}}
                {{--able_name="watchable"--}}
                {{--unfollow_text="Watching"--}}
                {{--follow_text="Watch"--}}
                {{--btn_size_class="btn white btn-sm"--}}
                {{--able_type="Kabooodle\Models\Flashsales"--}}
                {{--:able_id="'' + {{ $flashsale->id }}"--}}
                {{--:already_following="{{ $flashsale->is_watched ? 1 : 0 }}"--}}
                {{--endpoint="{{ apiRoute('flashsales.watchers.store', [$flashsale->id]) }}"--}}
        {{--></followable>--}}
        @if(webUser() && $flashsale->canSellerListToFlashsaleAnytime(webUser()->id))
            <span class="b-l m-l m-r"></span>
            <a href="{{ route('flashsales.edit', [$flashsale->getUUID()]) }}"
               class="btn white btn-sm"><i class="fa fa-cog" aria-hidden="true"></i></a>
        @endif
    </div>

@endsection


@section('body-content')
    <div class="row">
        <div class="col-md-3">
            <div class="navside white r box-shadow-z0 m-b">
                <div class="nav-border b-primary p-b-sm">
                    <ul class="nav">
                        <li class="nav-header hidden-folded"><span class="text-xs text-muted">Search Filters</span></li>
                        @include('listings.partials._categories_filter_options')
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
                        fetch_endpoint="{{ apiRoute('flashsales.show', [$flashsale->id]) }}"
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
