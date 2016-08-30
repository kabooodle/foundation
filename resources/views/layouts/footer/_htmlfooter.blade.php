@push('footer-scripts')
<script src="/assets/js/app.js?{{ getAppVersion() }}" data-turbolinks-track="reload"></script>
@endpush

@stack('footer-scripts')