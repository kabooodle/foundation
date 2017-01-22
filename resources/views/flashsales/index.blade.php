@extends('layouts.full', ['contentId' => 'flashsales_index'])


@section('body-menu')

    <div class="btn-toolbar center-block text-center">
        <div class="btn-group">
            <button type="button" id="" class="btn-toggle-filters btn btn-sm white">Filter Flash sales</button>
            <a href="{{ route('flashsales.create') }}" class="btn btn-sm primary pull-left" >Create New</a>

        </div>
    </div>
@endsection

@section('body-content')

            @include('flashsales.partials._indexaside', ['filters'=>[]])


            <div class="row content">
                <flashsales-cards
                        fetch_endpoint="{{ apiRoute('flashsales.index') }}"
                        watch_endpoint="{{ apiRoute('flashsales.watchers.store', ['::0::']) }}"
                        show_endpoint="{{ route('flashsales.show', ['::0::']) }}"
                ></flashsales-cards>
            </div>

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