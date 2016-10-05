@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/base.js') }}" data-turbolinks-track="reload"></script>
@endpush

@stack('footer-scripts')

<script src="{{ staticAsset('/assets/js/app.js') }}" data-turbolinks-track="reload"></script>