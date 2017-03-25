@extends('layouts.full', ['contentId' => 'flashsales_index'])


@section('body-menu')
    <div class=" center-block text-center ">
        <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
                <input type="text" name="name" v-model="search.sale_name" class="form-control" @keyup.enter="performSearch" placeholder="Search by name">
            </div>
        </div>
    </div>
@endsection

@section('body-content')

    <div class="row content">
        <flashsales-cards
                fetch_endpoint="{{ apiRoute('flashsales.index') }}"
                {{--watch_endpoint="{{ apiRoute('flashsales.watchers.store', ['::0::']) }}"--}}
                show_endpoint="{{ route('flashsales.show', ['::0::']) }}"
        ></flashsales-cards>
    </div>

    {{--<onboard-card--}}
            {{--:style="flashsales.length == 0 ? null : 'display: block;'"--}}
            {{--class="onboard-browseflashsales "--}}
    {{-->--}}
        {{--<template slot="title">Browse upcoming flash sales</template>--}}
        {{--<template slot="subtext">Users list their inventory online in a flash sale, which lasts for a specified period of time.--}}
            {{--<br>--}}
            {{--List only your items, or invite friends to list theirs with you too!--}}
            {{--<br>--}}
            {{--Flag flash sales so that you can be reminded about when they are open.--}}
        {{--</template>--}}
        {{--@if(webUser() && webUser()->hasAtLeastMerchantSubscription())--}}
        {{--<template slot="extra"><button type="button" class="btn btn-lg btn-grn m-b-2"><a href="{{ route('flashsales.create') }}">Cool, I'd like to create a flash sale</a></button></template>--}}
        {{--@endif--}}
    {{--</onboard-card>--}}

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsales-index.js') }}"></script>
<script>
    $(function () {
        $('.btn-toggle-filters').click(function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal')
        });

        $('.btn-toggle-reset').click(function(event){
            $('#navbarSide')
                    .find('input, select').val(null).trigger('change').find('option').prop('selected', false);
        });
    })
</script>
@endpush