@extends('layouts.minimal')

@push('header-styles')
<style>
    html {
        overflow: hidden;
    }
    html, body{
        height: 100%;
    }
    body {
        overflow: scroll;
        -webkit-overflow-scrolling: touch;
        margin: 0;
        padding: 0;
        background-image: url(/assets/images/home/closed_beta_bg.jpg);
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-attachment: fixed;
        background-position: center center;
        background-size:  cover;
        color: #fff;
    }
    body.container,
    #content {
        max-width: 100%;
        width: 100%;
        height: 100%;
    }
    body .app {
        padding-top: 0 !important;
        display: block;
    }
    h1, h2, h3 {
        color: #fff;
        font-family: 'Kreon', serif;
    }
    h1 {
        font-weight: 700;
        font-size: 80px;
        line-height: 100px;
        margin-bottom: 40px;
    }
    h6 {
        line-height: 36px;
        margin-bottom: 30px;
    }
    #form-alerts li {
        border-radius: 3px !important;
    }
    @media (max-width: 767px) {
        h1 {
            font-size: 50px;
            line-height: 80px;
        }
        h1,h6 {
            text-align: center;
        }
    }
    @media (max-width: @iphone-screen) {
        body {
            background-attachment: scroll;
        }
    }
    @media (max-width: 414px) {
        h1 {
            margin-top: 20px;
            font-size: 26px;
            line-height: 40px;
            margin-bottom: 20px;
        }
        h6 {
            font-size: 14px;
            line-height: 26px;
        }
    }
</style>
@endpush

@section('body-content')

    <div class="p-l-2 p-r-2 p-b-1" id="">
        <div class="row">
            <div class="col-md-7">
                <a class="text-left" href="/home">@include('partials._logo_svg_lg')</a>
                <div class="box">

                </div>
                <h1>
                    Our private beta is ending soon!
                </h1>
                <h6 class="">
                    We are currently finishing our private beta-testing
                    which means we will soon be opening up registration for everyone! Add your email address below, to be
                    notified of when registration is officially open.
                </h6>
                <h6 class="m-t-2">
                    Also note, we are putting the final touches on our iPhone app, and will notify and invite
                    anyone interested in testing as soon as the next version is available.
                </h6>
                <div class="form-group m-t-5" id="closed-beta">
                    <div id="form-alerts"></div>
                    <div class="m-t-1 input-group input-group-lg">
                        <input
                                @keyup="resetAlerts"
                                @keyup.enter="send('{{ apiRoute('closed-beta.store') }}', $event)"
                                type="email"
                                :class="actions.saving ? 'disabled' : null"
                                :disabled="actions.saving"
                                v-model="email" class="form-control" placeholder="Enter your email address">
                        <span class="input-group-btn">
                            <button
                                    :class="actions.saving ? 'disabled' : null"
                                    :disabled="actions.saving"
                                    class="btn primary"
                                    @click.prevent="send('{{ apiRoute('closed-beta.store') }}', $event)" type="button">Notify Me!
                            <spinny v-if="actions.saving"></spinny>
                            </button>
                        </span>
                    </div>
                </div>
                @include('partials._socialicons')
            </div>
        </div>
    </div>

@endsection

@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/closed-beta.js') }}"></script>
@endpush

