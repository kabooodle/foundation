<div class="prpl-800 dk pos-rlt app-footer" >
    <div class="p-md">
        <div class="p-a">
            <div class="row">
                <div class="col-xs-7">
                    <div class="clearfix">
                        <a href="/home" class="navbar-brand kabooodle-brand">
                            <span class="svg-logo">
                                @include('partials._logo_svg_lg')
                            </span>
                        </a>
                    </div>
                    <div class="m-v-md m-t-sm">
                        <p><strong>Everything &amp; More!<sup class="text-muted">&trade;</sup></strong></p>
                    </div>
                </div>

                <div class="col-xs-5">
                    <div class="clearfix">
                        <div class="pull-right">
                            @include('partials._socialicons')
                        </div>
                    </div>
                </div>
            </div>
            <div class="b b-b m-b m-t-sm"></div>
            <div class="clearfix">
                    <div class="pull-left text-sm-left text-xs-left">
                        <small class="text-muted">
                            &copy; Kabooodle,LLC {{ date('Y') }}
                            &middot; <a class="white-on-hover" href="/legal/privacy">Privacy</a>
                            &middot; <a class="white-on-hover" href="/legal/terms-service">Terms</a>
                        </small>
                    </div>
                <div class="pull-right text-sm-rirhg text-xs-right">
                    <small class="block text-xs text-muted">
                        v.{{ getAppVersion() }} @if(env('APP_ENV') <> 'production') r.{{ getReleaseVersion() }} @endif
                    </small>
                    <small class="block text-xs text-muted">
                        r.{{ executionTime() }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>



<a id="back-to-top" href="#" class="btn btn-grn btn-md back-to-top white" role="button"  style="
"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>


@push('footer-scripts')
<script>
    !function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!1,baseUrl:""},contact:{enabled:!0,formId:"6695d696-b1fc-11e6-91aa-0a5fecc78a4d"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});
    HS.beacon.config({
        modal: {{ Request::is('*home*') ? 'true' : 'false' }},
        icon: 'buoy',
        color: '#9446ed',
        attachment: true,
        poweredBy: false,
    });
    $(function(){
        HS.beacon.ready(function() {
            $('#hs-beacon').hide();
        });
        $('.js-toggle-help').click(function(){
            $('#hs-beacon').show();
            HS.beacon.toggle();
        });
        $(window).scroll(function () {
            var elem = '.app-footer';
            var pageTop = $(window).scrollTop();
            var pageBottom = pageTop + $(window).height();
            var elementTop = $(elem).offset().top;
            var elementBottom = elementTop + $(elem).height();
            if((elementTop <= pageBottom) && (elementBottom >= pageTop)){
                $('#back-to-top, #hs-beacon').addClass('beacon-offset');
            } else {
                $('#back-to-top, #hs-beacon').removeClass('beacon-offset');
            }

            if ($(this).scrollTop() > 800) {
                $('#back-to-top').fadeIn();
                $('#hs-beacon').addClass('beacon-move');
            } else {
                $('#back-to-top').fadeOut();
                $('#hs-beacon').removeClass('beacon-move');
            }
        });
        $('#back-to-top').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
</script>
@endpush