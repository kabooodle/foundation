var elixir = require('laravel-elixir');
// require('laravel-elixir-browserify-official');
require('laravel-elixir-vueify');

// elixir.config.production = true;,
elixir.config.assetsPath = 'resources/assets/';
elixir.config.appPath = '';
elixir.config.publicPath = 'public/assets/';
elixir.config.js.folder = elixir.config.css.folder = elixir.config.css.sass.folder = '/';

elixir(function (mix) {
    mix.copy(elixir.config.assetsPath + 'vendor/aws/aws-sdk-2.32.0.min.js',
        elixir.config.publicPath + 'js/aws-sdk-2.32.0.min.js');

    // Kabooodle VUE APP
    mix.browserify('app/app.js');
    mix.browserify('app/components/home.js');
    mix.browserify('app/components/closed-beta.js');

    mix.browserify('app/components/analytics/analytics-index.js');

    mix.browserify('app/components/referrals/referrals.js');

    // VUE Components
    mix.browserify('app/components/inventory/inventory-management.js');
    mix.browserify('app/components/inventory/manage/inventory-management-b.js');
    mix.browserify('app/components/inventory/inventory-create.js');
    mix.browserify('app/components/inventory/inventory-edit.js');

    mix.browserify('app/components/inventory-groupings/inventory-groupings-management.js');
    mix.browserify('app/components/inventory-groupings/inventory-groupings-simple.js');

    mix.browserify('app/components/listables/listables-archive.js');
    mix.browserify('app/components/listables/listables-detailed.js');
    mix.browserify('app/components/listables/listables-show.js');

    mix.browserify('app/components/profile/settings.js');
    mix.browserify('app/components/profile/emails.js');
    mix.browserify('app/components/profile/profile-notifications.js');
    mix.browserify('app/components/profile/profile-subscription.js');

    mix.browserify('app/components/shipping/shipping-profile.js');
    mix.browserify('app/components/shipping/shipping-create.js');
    mix.browserify('app/components/shipping/shipping-create.js');

    mix.browserify('app/components/sales/sales-management.js');

    mix.browserify('app/components/claims/claims-index.js');

    mix.browserify('app/components/listings/listing-detailed.js');
    mix.browserify('app/components/listings/listing-index-merchant.js');
    mix.browserify('app/components/listings/listing-items-page.js');
    mix.browserify('app/components/listings/listing-index.js');
    mix.browserify('app/components/listings/listing-create.js');

    mix.browserify('app/components/notices/notice-handler.js');

    mix.browserify('app/components/register/register.js');

    mix.browserify('app/components/sign-in/sign-in.js');
    mix.browserify('app/components/messenger/messenger-index.js');
    mix.browserify('app/components/messenger/messenger-thread-index.js');

    mix.browserify('app/components/flashsales/flashsale-create.js');
    mix.browserify('app/components/flashsales/flashsales-index.js');
    mix.browserify('app/components/flashsales/flashsale-items-page.js');
    mix.browserify('app/components/flashsales/flashsales-edit.js');

    mix.browserify('app/components/profile/profile.js');
    mix.browserify('app/components/profile/claims.js');
    mix.browserify('app/components/profile/claims-show.js');
    mix.browserify('app/components/profile/watching-items.js');

    mix.browserify('app/components/follow/follow.js');



    // Vendor File
    mix.browserify('app/vendor.js');

    mix
        .sass([
            'vendor/theme/scss/app.scss'
        ], elixir.config.publicPath + 'css/app.css')

        .sass([
            'vendor/theme/scss/pages/home.scss'
        ], elixir.config.publicPath + 'css/home.css')


        .sass([
            'vendor/theme/bootstrap/scss/bootstrap.scss',
            'vendor/lightbox/lightbox.css',
        ], elixir.config.publicPath + 'css/vendor.css')

        .styles([
            'vendor/selectizejs/dist/css/selectize.css',
            'vendor/selectizejs/dist/css/selectize.default.css',
            'vendor/datetimepicker/bootstrap-datetimepicker.min.css',
            'vendor/bootstrap-tataoggle/dist/titatoggle-dist.css',
            'vendor/bootstrap-touchspin/src/jquery.bootstrap-touchspin.css',
            'vendor/emojione/emojione.css',
            'vendor/bootstrap-select/bootstrap-select2-theme.css',
            'vendor/bootstrap-select/bootstrap-select2.css',
            'vendor/emojionearea/emojionearea.css',
            'vendor/bootstrap-slider/bootstrap-slider.css',
            'vendor/tablesaw/tablesaw.css',
            'vendor/theme/bootstrap-select/bootstrap-select.css',
            'vendor/perfectscroll/perfect-scrollbar.css',
            'vendor/introjs/introjs.css',
        ], elixir.config.publicPath + 'css/merchant.css')

        .scripts([
            'vendor/theme/scripts/ui-device.js',
            'vendor/theme/scripts/ui-form.js',
            'vendor/theme/scripts/ui-nav.js',
            'vendor/theme/scripts/ui-load.js',
            'vendor/theme/scripts/ui-screenfull.js',
            'vendor/theme/scripts/ui-scroll-to.js',
            'vendor/theme/scripts/ui-jp.js',
            'app/util.js'
        ], elixir.config.publicPath + 'js/base.js');
});