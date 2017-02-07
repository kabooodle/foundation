@extends('layouts.home')

@section('content')

    <?php

            $cards = [
                    'sales_analytics' => [
                            'title' => 'Sales analytics',
                            'body' => 'Understand which sales have better outcomes.  The who, what, where, when, why, and how.',
                            'icon'=>'friends_w_benefits.png'
                    ],
                'inventory' => [
                        'title' => 'Inventory',
                        'body' => 'Unlimited items, &amp; quantities. Includes tools to ensure you never oversell an item again.',
                    'icon'=>'friends_w_benefits.png'
                ],
                'wanted' => [
                        'title' => 'Hey, I wanted that!',
                        'body' => 'Run out of an item? Customers can automatically be notified the moment it becomes available!',
                    'icon' => 'hey_i_wanted_that.png'
                ],
                'shipping' => [
                        'title' => 'Shipping',
                        'icon' =>'shipping.png',
                    'body' =>'Ship single items or in bulk through USPS. Labels and automated tracking? You bet!'
                ],
                'groupings' => [
                        'title' => 'Inventory groupings',
                    'icon' => 'groupings.png',
                    'body' => 'Selling an outfit? Piece items together directly from your inventory.  Be creative, there are no limits!'
                ],
                    'claiming' => [
                            'title' => 'Claiming',
                            'icon' => 'claiming.png',
                            'body' => 'Accept and cancel claims quickly.  Guests can claim too. Everyone can claim!'
                    ],
                    'sell' => [
                            'title' => 'Sell and display',
                            'icon' => 'sell_and_display.png',
                            'body' => 'List your inventory to facebook, and to custom flash sales.  Add custom time constraints.'
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
                            'body' => 'You wont be spending hours trying to perform basic tasks.  We keep things simple.'
                    ],
                    'engagements' => [
                            'title' => 'Engagements',
                            'icon' => 'engagements.png',
                            'body' => 'Communicate directly with your customers everywhere, anytime.'
                    ],
                'friends' => [
                        'title' => 'Friends with benefits',
                    'icon' => 'friends_w_benefits.png',
                    'body' => 'Refer a friend to Kabooodle and earn a free month! This enrolls you in random giveaways as well.'
                ]
            ];


            ?>


    <section class="hero section section-w-bg">
        <div class="section-bg"></div>
        <header class="hero-header" role="navigation">
            <div class="container">
                <nav class="navbar navbar-toggleable-md">
                    <button class="navbar-toggler pull-right navbar-toggler-right" type="button" data-toggle="collapseable" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fa fa-bars" aria-hidden="true"></i>
                    </button>
                    <a class="navbar-brand kabooodle-brand" href="#">
                        @include('partials._logo_svg_lg')
                    </a>
                    <div class="collapseable navbar-collapse hidden" id="navbarNavDropdown" style="display: none;">
                        <button class="navbar-toggler " type="button" data-toggle="collapseable" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                            <i class="fa fa-times" aria-hidden="true"></i>
                        </button>
                        <ul class="navbar-nav nav">
                            <li class="nav-item"><a href="#solutions">Solutions</a></li>
                            <li class="nav-item"><a href="#pricing">Pricing</a></li>
                            <li class="nav-item"><a href="#services">Services</a></li>
                            <li class="nav-item"><a href="#about">About us</a></li>
                            <li class="nav-item"><a href="#ready">Sign up</a></li>
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
                <h2 class="section-sub-title">Connect with clients, manage inventory, track and ship sales. <span class="sub-title-more">Everything &amp; More!</span></h2>
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
        <div id="pricing"  class="section-bg"></div>
        <sup class="achievement achievement-yr animated " style="position:absolute; top: 14px; left: 50%; display:none; font-size: 90%; ">
            <span class="text-center primary text-white p-a-sm r-3x" style="margin-left: -120px;">Achievement unlocked! <strong class="_800">20%</strong> off!</span>
        </sup>
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
                        <li>Track Purchase/Claim Anywhere</li>
                        <li>Shipment Tracking Notifications</li>
                        <li>Follow Sales, Items, Users</li>
                        <li>Always Free. Forever. Free!</li>
                        </ul>
                        <div class="try-wrapper text-center center-block">
                            <button class="_700 btn warning btn-lg">Absolutely, Yes.</button>
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
                            <li>Sales and Item Analytics</li>
                            <li>Overselling Prevention</li>
                            <li>Custom Social Features</li>
                            <li>Inventory Groupings</li>
                        </ul>
                        <div class="try-wrapper text-center center-block">
                            <button class="_700 btn warning btn-lg">Try Merchant</button>
                        </div>
                    </div>

                </div>
                <div class="col-md-4 col-bg col-price  col-price-merchant-plus">
                    <h2 class="subscription-name text-center">Merchant Plus</h2>
                    <h4 class="the-price text-center">
                        <span class="price-mo">$15
                                                    <span class="block text-md">per month</span>
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
                            <li>Ship in Bulk or Single</li>
                            <li>Track Shipments Anywhere</li>
                            <li>Print Shipping Labels</li>
                            <li>$.03 cents per label</li>
                        </ul>
                        <div class="text-center try-wrapper center-block">
                            <button class="_700 btn warning btn-lg">Try Merchant Plus</button>
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
                        <img src="/assets/images/home/icons/cloud_secure.png" class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Secure Infrastructure</h4>
                            <p class="card-text">We’ve employed secure connections where information is encrypted to ensure maximum uptime and safety of your records.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4  b-r b-r-2x">
                    <div class="card no-border card-centered card-lg">
                        <img src="/assets/images/home/icons/support.png" class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Friendly Support</h4>
                            <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card no-border card-centered card-lg">
                        <img src="/assets/images/home/icons/device_friendly.png" class="card-img-top">
                        <div class="card-block">
                            <h4 class="card-title">Device Friendly</h4>
                            <p class="card-text">Whether using a PC, Mac, or mobile device, your data is always accessible and in sync.  Our native iPhone app is also handy.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <section class="section section-w-bg section-about">
        <div class="section-bg"></div>
        <h1 id="about" class="section-title text-center">About us</h1>
        <h2 class="section-sub-title text-center">We are a dedicated team, ready and available to help you continue to see success! We have over 26 years enterprise software and business experience, helping to ensure successful and happy people.</h2>
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



    <section class="section section-w-bg section-ready">
        <div class="section-bg"></div>
        <h1 id="ready" class="section-title text-center">Ready to get started?</h1>
        <h2 class="section-sub-title text-center">Register and start selling and buying in minutes. No card required.</h2>
        <div class="cta-group text-center">
            <button class="btn btn-lg cta-ready">Try it for free</button>
            <button class="btn btn-lg cta-questions">Have questions?</button>
        </div>
    </section>


    @include('layouts.footer._footermeta')

@endsection


@push('footer-scripts')

<script>
    $(function() {

        let collapseable_toggler = $('[data-toggle="collapseable"]');
        let collapseable_el = $('.collapseable');

        collapseable_toggler.click(function(){
            console.log('hi');
            collapseable_el.is(':visible') ? collapseable_el.removeClass('show').hide() : collapseable_el.addClass('show').show();
        });

        let pricing_el = $('[data-type="pricing-toggler"]');
        pricing_el.change(function(e){
            if (pricing_el.is(':checked')) {
                $('.price-mo').hide();
                $('.price-yr').show();
                $('.achievement-yr').show().removeClass('bounceOutUp').addClass('bounceInDown');
            } else {
                $('.price-mo').show();
                $('.price-yr').hide();
                $('.achievement-yr').removeClass('bounceInDown').addClass('bounceOutUp');
            }
        });

        $('a[href*=#]:not([href=#])').click(function() {
            if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'')
                    || location.hostname == this.hostname) {

                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
                if (target.length) {
                    $('html,body').animate({
                        scrollTop: target.offset().top
                    }, 1000, function(){
                        collapseable_el.removeClass('show').hide();
                    });
                    return false;
                }
            }
        });
    });
</script>

@endpush