@extends('layouts.full', ['contentId' => 'referrals_index'])


@section('body-content')


    <div class="onboard-card onboard_wrapper onboard-referrals">
        <div class="onboard-body text-center">
            <h1 class="onboard-card-title">
                Share {{ env('APP_NAME') }} and Earn!
            </h1>
            <h2 class="onboard-card-sub-title text-center m-b-3">
                For every friend you refer whom joins {{ env('APP_NAME') }}, we'll credit your account a <strong>free
                    month, up to 6 months.</strong>
                <br>
                For each additional referral, your name will be entered in our biannual drawing to win
                <br>
                4 tickets to Disneyland or a $500 gift card, and other items!
            </h2>
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="input-group">
                        <input type="text" readonly class="form-control readonly-lt" id="link-text"
                               placeholder="Search for..." value="{{ route('invite.index', [webUser()->username]) }}">
                        <span class="input-group-btn">
                            <button data-animation="false" class="btn success" data-clipboard-target="#link-text"
                                    type="button">
                                <small class="small _500">COPY</small>
                            </button>
                        </span>
                    </div>
                    <div class="m-t-3">
                        Share this on
                        <button id="btn-social-facebook"
                                @click.prevent="triggerFbShare('{{ route('invite.index', [webUser()->username]) }}', $event)"
                                class="btn btn-icon btn-social btn-sm white" type="button"><i
                                    class="fa fa-facebook"></i> <i class="fa fa-facebook indigo"></i></button>
                        <a id="btn-social-twitter" class="btn btn-icon btn-social btn-sm white"
                           href="https://twitter.com/home?status={{ route('invite.index', [webUser()->username]) }}"
                           target="_blank"><i class="fa fa-twitter"></i> <i class="fa fa-twitter blue"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @if(webUser()->referrals->count() > 0)
        <div class="box m-t-3">
            <div class="box-body">
                <div class="row">
                    @foreach(webUser()->referrals as $referral)
                        <div class="col-md-4">
                            <ul class="list p-b-0 b-a {{ $referral->subscribed('main') ? 'b-success' : null }}">
                                <li class="list-item">
                                    <a href="http://kabooodle.dev/shop/jaketoolson" class="list-left">
                            <span class="w-40 avatar">
                              <img src="https://placekitten.com/g/32/32" alt="...">
                                                        </span>
                                    </a>
                                    <div class="list-body">
                                        <div class="_500"><a href="">{!! $referral->name !!}</a></div>
                                        <div class="text-muted">
                                            joined: {{ $referral->created_at->diffForHumans() }}</div>
                                        <div class="text-muted text-sm">{{ $referral->myAccountIsAQualifyingReferral() ? 'Qualified!' : 'not yet qualified' }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <div class="box m-t-3">
            <div class="box-body">
                <p class="text-center m-b-0">No referrals yet!</p>
            </div>
        </div>
    @endif

@endsection

@push('footer-scripts')
<script src="{{staticAsset('/assets/js/referrals.js')}}"></script>
@endpush