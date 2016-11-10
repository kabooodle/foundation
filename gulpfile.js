var elixir = require('laravel-elixir');
require('laravel-elixir-vueify');

// elixir.config.production = true;,
elixir.config.assetsPath = 'resources/assets/';
elixir.config.appPath = '';
elixir.config.publicPath = 'public/assets/';
elixir.config.js.folder = elixir.config.css.folder = elixir.config.css.sass.folder = '/';

elixir(function (mix) {

    // Kabooodle VUE APP
    mix.browserify('app/app.js');

    // VUE Components
    mix.browserify('app/components/inventory/inventory-management.js');
    mix.browserify('app/components/inventory/inventory-create.js');
    mix.browserify('app/components/inventory/inventory-edit.js');
    mix.browserify('app/components/profile/settings.js');
    mix.browserify('app/components/profile/profile-notifications.js');
    mix.browserify('app/components/shipping/shipping-create.js');
    mix.browserify('app/components/shipping/shipping-create.js');

    // Vendor File
    mix.browserify('app/vendor.js');

    mix
        .sass([
            'vendor/theme/scss/app.scss'
        ], elixir.config.publicPath + 'css/app.css')

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
            'vendor/emojionearea/emojionearea.css'
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

    ], elixir.config.publicPath + 'js/base.js')
});