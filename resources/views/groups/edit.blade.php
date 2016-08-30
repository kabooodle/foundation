@extends('layouts.full')

@section('body-content')

    <div class="app" id="app">

        <div id="content" class="app-content box-shadow-z0" role="main">

            @include('layouts.header._header')

            <div ui-view class="app-body" id="view">



                <div class="row-col">

                    @include('inventory.partials._lh_nav')

                    <div class="col-sm">
                        <div ui-view="" class="padding pos-rlt">

                            <div class="row">
                                <p>{!! $item->name !!}</p>

                                {!! nl2br($item->description) !!}
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection