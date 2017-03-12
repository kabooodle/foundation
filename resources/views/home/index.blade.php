@extends('layouts.home')

@push('facebook-tags')
<meta property="og:image" content="{{ staticAsset('/assets/images/home/section_hero_bg.jpg') }}" />
@endpush

@section('content')

    <sup class="achievement achievement-yr animated "
         style="position:fixed; top: 18px; left: 50%; display:none; font-size: 90%; z-index: 9999 ">
        <span class="text-center primary text-white p-a-sm r-3x"
              style="margin-left: -120px;">Achievement unlocked! <strong class="_800">20%</strong> off!</span>
    </sup>


    <?php

    $cards = [
        'sales_analytics' => [
            'title' => 'Sales analytics',
            'body' => 'Understand <span class="_500">which sales yield better results.</span>  Know the who, what, where, when, why, and how.',
            'icon' => 'salesanalytics.png'
        ],
        'inventory' => [
            'title' => 'Inventory',
            'body' => 'Unlimited items, &amp; quantities. Includes tools to ensure you <span class="_500">never oversell an item again.</span> Unlimited photos? Also yes.',
            'icon' => 'inventory.png'
        ],
        'wanted' => [
            'title' => 'Hey, I wanted that!',
            'body' => 'Run out of an item? Customers can automatically be notified the moment a watched item becomes available!',
            'icon' => 'hey_i_wanted_that.png'
        ],
        'shipping' => [
            'title' => 'Shipping',
            'icon' => 'shipping.png',
            'body' => 'Ship single items or in bulk through USPS. Labels, automated tracking, <span class="_500">multiple package types</span>, and more.'
        ],
        'groupings' => [
            'title' => 'Inventory groupings',
            'icon' => 'groupings.png',
            'body' => 'Selling an outfit? Piece items together directly from your inventory.  <span class="_500">No overselling</span>, create custom prices.'
        ],
        'claiming' => [
            'title' => 'Claiming',
            'icon' => 'claiming_a.png',
            'body' => 'Accept and cancel claims quickly.  Guests can claim too. Everyone can claim!'
        ],
        'sell' => [
            'title' => 'Sell and display',
            'icon' => 'sell_and_display.png',
            'body' => 'List your inventory to facebook, custom flash sales, anything, anywhere.  <span class="_500">Customizable schedule and claim times.</span>'
        ],
        'notifications' => [
            'title' => 'Notifications',
            'icon' => 'notifications.png',
            'body' => 'Be in the know.  Customize the notifications you receive via email and text.'
        ],
        'flashsales' => [
            'title' => 'Flash sales',
            'icon' => 'flashsales.png',
            'body' => 'Host an online sale anytime.  Invite your friends to list their inventory in the sales with you!'
        ],
        'relax' => [
            'title' => 'Relax',
            'icon' => 'relax.png',
            'body' => 'You wont be spending hours trying to perform basic tasks.  <span class="_500">We keep things simple.</span>'
        ],
        'engagements' => [
            'title' => 'Engagements',
            'icon' => 'engagements.png',
            'body' => 'Whether through a listing, or directly, communicate instantly with your customers anytime.'
        ],
        'friends' => [
            'title' => 'Friends with benefits',
            'icon' => 'friends_w_benefits.png',
            'body' => 'Earn a free month for every friend you refer to Kabooodle! This enrolls you in random giveaways as well.<sup>*</sup>'
        ]
    ];


    ?>


    <section class="hero section section-w-bg">
        <div class="section-bg"></div>
        <header class="hero-header" role="navigation">
            <div class="container">
                <nav class="navbar navbar-toggleable-md">
                    <button class="navbar-toggler pull-right navbar-toggler-right" type="button"
                            data-toggle="collapseable" data-target="#navbarNavDropdown"
                            aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                    <a class="navbar-brand kabooodle-brand" href="#">
                        @include('partials._logo_svg_lg')
                    </a>
                    <div class="collapseable navbar-collapse hidden" id="navbarNavDropdown" style="display: none;">
                        <button class="navbar-toggler " type="button" data-toggle="collapseable"
                                data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                                aria-label="Toggle navigation">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                        <ul class="navbar-nav nav">
                            <li class="nav-item"><a href="#solutions">Solutions</a></li>
                            <li class="nav-item"><a href="#pricing">Pricing</a></li>
                            <li class="nav-item"><a href="#services">Services</a></li>
                            <li class="nav-item"><a href="#about">About us</a></li>
                            <li class="nav-item"><a href="#contact">Contact us</a></li>
                            <li class="nav-item"><a href="/auth/register">Sign up</a></li>
                            <li class="nav-item">
                                @if(user($ignoreApiAuth = true))
                                    <a href="/profile">Account</a>
                                @else
                                    <a href="/auth/login">Sign in</a>
                                @endif
                            </li>
                        </ul>
                    </div>

                </nav>
            </div>
        </header>
        <div class="container">
            <div class="hero-inner">
                <h1 class="hero-title section-title">Streamline your direct sales business.</h1>
                <h2 class="section-sub-title">Connect with clients, manage inventory, track and ship sales. <span
                            class="sub-title-more">Everything &amp; More!</span></h2>
                <a href="#pricing" class="btn cta prpl-primary btn-lg">
                    Get started for free
                </a>
            </div>
        </div>
    </section>

    <section class="section section-solutions">
        <h1 id="solutions" class="section-title text-center">Solutions</h1>
        <h2 class="section-sub-title text-center">The pocket knife of solutions, without the useless extras.</h2>
        <div class="container wrapper-border p-a-3">
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['inventory']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['sell']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['sales_analytics']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['groupings']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['shipping']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['flashsales']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['claiming']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['notifications']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['wanted']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['friends']])
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['engagements']])
                </div>
                <div class="col-md-6">
                    @include('home.partials._solutioncard', ['_card' => $cards['relax']])
                </div>
            </div>
        </div>
    </section>

    <section class="section section-pricing section-w-bg">
        <div id="pricing" class="section-bg"></div>
        <h1 class="section-title text-center">Simple pricing</h1>
        <h2 class="section-sub-title text-center">You can upgrade, downgrade, cancel, and dance at anytime.</h2>
        <div class="row">
            <div class="col-xs-4"></div>
            <div class="col-xs-4 text-center">
                <div class="checkbox checkbox-slider--b-flat checkbox-slider-lg" style="width:100px; margin: 0 auto;">
                    <label>
                        <input
                                data-type="pricing-toggler"
                                type="checkbox"
                                value="1"
                        ><span></span>
                    </label>
                </div>
            </div>
            <div class="col-xs-4"></div>
        </div>

        <div class="container">
            <div class="row row-pricing row-eq-height">
                <div class="col-md-4 col-bg col-price col-price-basic ">
                    <h2 class="subscription-name text-center">Basic</h2>
                    <h4 class="the-price text-center">
                        <span class="price-mo">FREE</span>
                        <span class="price-yr" style="display: none">FREE</span>
                    </h4>
                    <div class="features">
                        <ul class="">
                            <li>Make Purchases/Claims</li>
                            <li class="_700">Track Purchase/Claim Anywhere</li>
                            <li class="_700">Shipment Tracking Notifications</li>
                            <li class="_700">Follow Sales, Items, Users</li>
                            <li>Always Free. Forever. Free!</li>
                        </ul>
                        <div class="try-wrapper text-center center-block">
                            <a href="/auth/register?a=basic" class="_700 btn warning btn-lg">Absolutely, Yes.</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-bg col-price col-price-merchant">
                    <h2 class="subscription-name text-center">Merchant</h2>
                    <h4 class="the-price text-center">
                        <span class="price-mo">$10
                            <span class="block text-md">per month</span>
                        </span>
                        <span class="price-yr" style="display: none">$96
                           <span class="block text-md">per year</span>
                        </span>
                    </h4>
                    <div class="features">
                        <ul class="">
                            <li>Unlimited Inventory Items</li>
                            <li>Unlimited Sales</li>
                            <li class="_700">Sales and Item Analytics</li>
                            <li class="_700">Overselling Prevention</li>
                            <li>Custom Social Features</li>
                            <li class="_700">Inventory Groupings</li>
                        </ul>
                        <div class="try-wrapper text-center center-block">
                            <a href="/auth/register?a=merchant" class="_700 btn warning btn-lg">30 Day FREE Trial</a>
                        </div>
                    </div>

                </div>
                <div class="col-md-4 col-bg col-price  col-price-merchant-plus">
                    <h2 class="subscription-name text-center">Merchant Plus</h2>
                    <h4 class="the-price text-center">
                        <span class="price-mo">
                            $15<span class="block text-md">per month</span>
                        </span>
                        <span class="price-yr " style="display: none">$144
                        <span class="block text-md">per year</span>
                        </span>
                    </h4>
                    <div class="features">
                        <ul class="">
                            <li><strong>All Merchant Features</strong></li>
                            <li><strong>+</strong></li>
                            <li>Ship Directly Through USPS</li>
                            <li class="_700">Ship in Bulk or Single</li>
                            <li>Printable Shipping Labels</li>
                            <li class="_700">Multiple Package Types</li>
                            <li>$.03 Cents Per Label</li>
                        </ul>
                        <div class="text-center try-wrapper center-block">
                            <a href="/auth/register?a=merchant_plus" class="_700 btn warning btn-lg">30 Day FREE
                                Trial</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-services">
        <h1 id="services" class="section-title text-center">Services</h1>
        <h2 class="section-sub-title text-center">Yup, services built to support our robust solutions :)</h2>
        <div class="container">
            <div class="row">
                <div class="col-md-4 b-r b-r-2x">
                    <div class="card no-border card-centered card-lg">
                        <img width="160" src="{{staticAsset('/assets/images/home/icons/cloud_secure.png')}}"
                             class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Infrastructure</h4>
                            <p class="card-text">We strive to provide a secure, centralized system,  where all your critical data
                                can be stored, helping to keep you focused through reduced complexities and fast service. </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4  b-r b-r-2x">
                    <div class="card no-border card-centered card-lg">
                        <img width="160" src="{{staticAsset('/assets/images/home/icons/support.png')}}"
                             class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Professional Support</h4>
                            <p class="card-text">Our knowledgeable support team is eager to assist you with any
                                questions you may have. If you have suggestions, ideas, complaints, questions, or are bored, we'd love to chat!</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card no-border card-centered card-lg">
                        <img width="160" src="{{staticAsset('/assets/images/home/icons/device_friendly.png')}}"
                             class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Device Friendly</h4>
                            <p class="card-text">Whether using a PC, Mac, or mobile device, your data is always
                                accessible and in sync. Our native iPhone app is nearing completion and brings
                                simplicity through mobility.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section section-w-bg section-about">
        <div class="section-bg"></div>
        <h1 id="about" class="section-title text-center">About us</h1>
        <h2 class="section-sub-title text-center">We are a dedicated team, ready and available to help you continue to
            see success! We have over 26 years enterprise software and business experience, helping to ensure successful
            and happy people.</h2>
        <div class="row row-stats">
            <div class="col-sm-3">
                <h1 class="stat-title text-center">5</h1>
                <h2 class="stat-subtitle text-center">Team members</h2>
            </div>
            <div class="col-sm-3">
                <h1 class="stat-title text-center">11</h1>
                <h2 class="stat-subtitle text-center">Children</h2>
            </div>
            <div class="col-sm-3">
                <h1 class="stat-title text-center">4</h1>
                <h2 class="stat-subtitle text-center">Languages</h2>
            </div>
            <div class="col-sm-3">
                <h1 class="stat-title text-center">46</h1>
                <h2 class="stat-subtitle text-center">Apple products</h2>
            </div>
        </div>
    </section>

    <section class="section section-w-bg section-contact">
        <div class="section-bg"></div>
        <h1 id="contact" class="section-title text-center">Contact us</h1>
        <h2 style="width: 80%; margin: 0 auto;" class="section-sub-title text-center">Have a question? Have a concern? Want some free Kabooodle stickers?</h2>
        <div class="container m-t-3">
            <div class="text-center center-block center">
                <button
                        data-backdrop="static"
                        data-keyboard="false"
                        style="position: relative;"
                        data-toggle="modal"
                        data-target="#contact_form"
                        class="btn-hs-open primary btn btn-lg">Click here to get in touch</button>
                <div class="block m-t-1">
                    @include('partials._socialicons', ['_btnclass' => 'btn-lg white'])
                </div>
            </div>
        </div>
    </section>

    <section class="section section-w-bg section-ready">
        <div class="section-bg"></div>
        <h1 id="ready" class="section-title text-center">Ready to get started?</h1>
        <h2 class="section-sub-title text-center">Register to begin selling and buying in minutes. No card
            required.</h2>
        <div class="cta-group text-center">
            <a href="/auth/register?a=merchant" class="btn btn-lg cta-ready">Start my 30 day free trial!</a>
        </div>
    </section>

    @include('layouts.footer._footermeta')


    <div class="modal" id="contact_form" tabindex="-1" role="dialog" aria-labelledby="contact_form" aria-hidden="true">
        <div class="row-col h-v">
            <div class="row-cell v-m">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-body p-t-1 p-l-2 p-r-2">
                            <div id="form-alerts" class="r m-b-1"></div>
                            <div class="form-group">
                                <input type="text" v-model="contact_fields.name" class="form-control" placeholder="Your name">
                            </div>
                            <div class="form-group">
                                <input type="email" v-model="contact_fields.email"  class="form-control" placeholder="Email address">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="message" v-model="contact_fields.message" placeholder="How can we help you?"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                    :disabled="actions.saving"
                                    :class="actions.saving ? 'disabled' : null"
                                    @click.prevent="sendContact('{{ apiRoute('contact.store') }}', $event)"
                                    type="button"
                                    class="btn btn-sm primary">Send
                                <spinny v-if="actions.saving"></spinny>
                            </button>
                            <button
                                    :disabled="actions.saving"
                                    :class="actions.saving ? 'disabled' : null"
                                    type="button"
                                    class="btn btn-sm white"
                                    data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@push('footer-scripts')
<script src="{{ staticAsset('/assets/js/home.js') }}"></script>
@endpush