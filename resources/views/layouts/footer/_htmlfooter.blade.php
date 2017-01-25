@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/base.js') }}" data-turbolinks-track="reload"></script>
<script src="{{ staticAsset('/assets/js/notice-handler.js') }}"></script>
@endpush

<script src="{{ staticAsset('/assets/js/app.js') }}"></script>
<script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');ga('create', 'UA-87479908-1', 'auto');ga('send', 'pageview');</script>
@stack('footer-scripts')