@extends('layouts.full', ['contentId' => 'analytics_index'])

@push('header-styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/c3/0.4.10/c3.min.css" rel="stylesheet" type="text/css">
@endpush

@section('body-content')

    <div class="row">
        <div class="col-md-3">
            <div id="sum_total"></div>
        </div>
        <div class="col-md-3">
            <div id="sum_dollars"></div>
        </div>

        <div class="col-md-3">

        </div>
    </div>

    <div class="box white">
        <div class="box-header">

        </div>
        <div class="box-body">
            <div id="my_chart"></div>
        </div>
    </div>


@endsection

@push('footer-scripts')
<script>
    var KCONFIG = {
        read_key : '{{ webUser()->keen_access_key }}',
        project_id: '{{ env('KEENIO_PROJECTID') }}'
    };
</script>
<script src="{{ staticAsset('/assets/js/analytics-index.js') }}"></script>
@endpush
