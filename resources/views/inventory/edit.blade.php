@extends('layouts.full')


{{--@section('body-content-left-nav')--}}
    {{--<div class="box no-shadow m-b-0">--}}
        {{--<div class="box-body" id="images_el" >--}}
            {{--<div v-if="images.length">--}}
                {{--<div class="item" v-for="(index,image) in images">--}}
                    {{--<div class="item-overlay active p-l p-r " style="z-index: 999;">--}}
                        {{--<a type="button"--}}
                           {{--style=""--}}
                           {{--v-on:click="deleteImage(image, $event)"--}}
                           {{--:data-id="image.id"--}}
                           {{--class="pull-right text-danger"><i class="fa fa-times fa-fw"></i></a>--}}
                        {{--<span class="pull-left label dark-white text-color">@{{ images.length - $index }}</span>--}}
                    {{--</div>--}}
                    {{--<div class="thumbnail" >--}}
                        {{--<img--}}
                                {{--:src="image.location"--}}
                                {{--:data-gallery="'gallery_'+item.id"--}}
                                {{--class="w-full"--}}
                                {{--data-toggle="lightbox"--}}
                                {{--:data-remote="image.location"/>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div v-else>--}}
                {{--No Images--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endsection--}}

@section('body-content')
    <div class="">
        <ul class="nav nav-tabs nav-tabs-white">
            <li class="nav-item" >
                <a class="nav-link active no-border " style="border: none;" href="#">Item Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link no-border" style="border: none;" href="#">Current Listings</a>
            </li>
        </ul>
    </div>

    <div class="box no-shadow">
        <div class="box-body">
        @include('inventory.partials._edit', ['item' => $item, 'styles' => $styles])
        </div>
    </div>

@endsection
