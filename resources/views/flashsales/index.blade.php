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