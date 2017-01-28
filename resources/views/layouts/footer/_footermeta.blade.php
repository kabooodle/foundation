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