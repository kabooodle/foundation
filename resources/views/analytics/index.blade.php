@extends('layouts.full', ['contentId' => 'analytics_index'])


@section('body-content')

    Coming soon.

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
