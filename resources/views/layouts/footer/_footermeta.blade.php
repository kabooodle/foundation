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
                        <p><strong>Everything &amp; More!</strong></p>

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

<style>
    #hs-beacon.beacon-move iframe{
        right: 64px !important;
        -webkit-transition: all .5s ease;
        -moz-transition: all .5s ease;
        -o-transition: all .5s ease;
        -ms-transition: all .5s ease;
        transition: all .5s ease;
    }
    #hs-beacon iframe{
        right: 15px !important;
        -webkit-transition: all .5s ease;
        -moz-transition: all .5s ease;
        -o-transition: all .5s ease;
        -ms-transition: all .5s ease;
        transition: all .5s ease;
    }
</style>

<a id="back-to-top" href="#" class="btn btn-grn btn-md back-to-top white" role="button"  style="
box-shadow: 0 2px 5px 0 rgba(0,0,0,.26);
position: fixed; height:54px; bottom: 25px; line-height: 34px; right: 15px; color:white; z-index: 9999; display: none; border-radius:100%;
width:54px;
"><i class="fa fa-chevron-up" aria-hidden="true"></i></a>


@push('footer-scripts')

<script>
    !function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!1,baseUrl:""},contact:{enabled:!0,formId:"6695d696-b1fc-11e6-91aa-0a5fecc78a4d"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});
    HS.beacon.config({
        icon: 'buoy',
        color: '#9446ed',
        attachment: true,
        poweredBy: false,
    });
    $(function(){
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                $('#back-to-top').fadeIn();
                $('#hs-beacon').addClass('beacon-move');
            } else {
                $('#back-to-top').fadeOut();
                $('#hs-beacon').removeClass('beacon-move');
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