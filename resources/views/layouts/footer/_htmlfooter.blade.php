@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/base.js') }}" data-turbolinks-track="reload"></script>
@endpush

<script src="{{ staticAsset('/assets/js/app.js') }}"></script>
{!! Analytics::render() !!}
@stack('footer-scripts')