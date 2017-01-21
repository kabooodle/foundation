@extends('layouts.full', ['contentId' => 'flashsales_index'])


@if($data->count() > 0)
@section('body-menu')
    <div class="btn-toolbar center-block text-center">
        <div class="btn-group">
            <a href="{{ route('flashsales.create') }}" class="btn btn-sm primary pull-left" >Create New</a>

        </div>
    </div>
@endsection
@endif

@section('body-content')

            <div class="row content">
                <flashsales-cards
                        fetch_endpoint="{{ apiRoute('flashsales.index') }}"
                        watch_endpoint="{{ apiRoute('flashsales.watchers.store', ['::0::']) }}"
                        show_endpoint="{{ route('flashsales.show', ['::0::']) }}"
                ></flashsales-cards>
            </div>

            {{--<div class="row">--}}
                {{--<div class="col-sm-6 col-sm-offset-3">--}}
                    {{--<div class="white box padding">--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-7">--}}
                                {{--<img src="{{ staticAsset('/assets/images/online-shop.jpg') }}" class="" width="500">--}}
                            {{--</div>--}}
                            {{--<div class="col-md-5">--}}
                                {{--<div class="center-block text-center">--}}
                                    {{--<h3 style="margin: 50% 0;"><a href="{{ route('flashsales.create') }}" class="btn btn-lg success">Create New Flash Sale</a></h3>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}


@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/flashsales-index.js') }}"></script>
@endpush