<div class="prpl-800 dk pos-rlt app-footer" >
    <div class="p-md">
        <div class="p-a">
            <div class="row">
                <div class="col-md-9">
                    <div class="clearfix">
                        <a class="navbar-brand kabooodle-brand">
                            <span class="svg-logo">
                                @include('partials._logo_svg_lg')
                            </span>
                        </a>
                    </div>
                    <div class="m-v-md m-t-xs">
                        <p>
                            <strong>Everything &amp; More!</strong>
                        </p>

                        <div class="text-sm-left text-xs-left">
                            <small class="text-muted">&copy; Kabooodle,LLC 2016</small> <small class="block text-xs text-muted">v.{{ getAppVersion() }} @if(env('APP_ENV') <> 'production') r.{{ getReleaseVersion() }} @endif</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="clearfix">
                        <div class="pull-right">
                            <a class="btn btn-icon btn-social btn-sm white" href="https://www.facebook.com/kabooodle"><i class="fa fa-facebook"></i> <i class="fa fa-facebook indigo"></i></a> <a class="btn btn-icon btn-social btn-sm white" href=""><i class="fa fa-twitter"></i> <i class="fa fa-twitter blue"></i></a> <a class="btn btn-icon btn-social btn-sm white" href=""><i class="fa fa-google-plus"></i> <i class="fa fa-google-plus red"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<a id="back-to-top" href="#" class="btn btn-grn btn-md back-to-top" role="button"  style="position: fixed; bottom: 16px; right: 16px; z-index: 9999; display: none;"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>


@push('footer-scripts')
<script>
    $(function(){
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
        // scroll body to 0px on click
        $('#back-to-top').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
</script>
@endpush