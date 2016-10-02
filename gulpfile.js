var elixir = require('laravel-elixir');

// elixir.config.production = true;
elixir.config.assetsPath = 'resources/assets/';
elixir.config.appPath = '';
elixir.config.publicPath = 'public/assets/';
elixir.config.js.folder = elixir.config.css.folder = elixir.config.css.sass.folder = '/';

elixir(function (mix) {
    mix

        .sass([
            'vendor/theme/scss/app.scss'
        ], elixir.config.publicPath + 'css/app.css')

        .sass([
            'vendor/theme/bootstrap/scss/bootstrap.scss',
            'vendor/lightbox/lightbox.css'
        ], elixir.config.publicPath + 'css/vendor.css')

        .styles([
            // 'vendor/theme/scripts/jquery/select2/dist/css/select2.css',
            // 'vendor/theme/scripts/jquery/select2-bootstrap-theme/dist/select2-bootstrap.css',
            // 'vendor/theme/scripts/jquery/select2-bootstrap-theme/dist/select2-bootstrap.4.css'
            'vendor/selectizejs/dist/css/selectize.css',
            'vendor/selectizejs/dist/css/selectize.default.css',
            'vendor/datetimepicker/bootstrap-datetimepicker.min.css',
            'vendor/bootstrap-touchspin/src/jquery.bootstrap-touchspin.css',
            'vendor/emojione/emojione.css'
            // 'vendor/selectizejs/dist/css/selectize.bootstrap3.css'
        ], elixir.config.publicPath + 'css/merchant.css')

        .scripts([
            'vendor/theme/scripts/jquery/jquery/dist/jquery.js',
            'vendor/vuejs/vue.1.0.26.js',
            'vendor/vuejs/vue-resource.js',
            // 'vendor/turbolinks/turbolinks.5.0.0.js',
            'vendor/theme/scripts/jquery/tether/dist/js/tether.min.js',
            'vendor/theme/scripts/jquery/bootstrap/dist/js/bootstrap.js',
            'vendor/theme/scripts/jquery/underscore/underscore-min.js',
            'vendor/theme/scripts/jquery/moment/moment.js',
            'vendor/datetimepicker/bootstrap-datetimepicker.min.js',
            // 'vendor/theme/scripts/jquery/PACE/pace.min.js',
            // 'vendor/theme/scripts/jquery/screenfull/dist/screenfull.min.js',
            'vendor/jquery/noty/packaged/jquery.noty.packaged.js',
            'vendor/clipboard/clipboard.js',
            'vendor/lightbox/lightbox.js'
        ], elixir.config.publicPath + 'js/vendor.js')

        .scripts([
            // 'vendor/theme/scripts/jquery/select2/dist/js/select2.full.js',
            'vendor/selectizejs/dist/js/standalone/selectize.js',
            'vendor/bootstrap-touchspin/src/jquery.bootstrap-touchspin.js',
            'app/s3uploader.js',
            'vendor/emojione/emojione.js'
        ], elixir.config.publicPath + 'js/merchant.js')

        .scripts([
            'vendor/theme/scripts/ui-device.js',
            'vendor/theme/scripts/ui-form.js',
            'vendor/theme/scripts/ui-nav.js',
            'vendor/theme/scripts/ui-load.js',
            'vendor/theme/scripts/ui-screenfull.js',
            'vendor/theme/scripts/ui-scroll-to.js',
            'vendor/theme/scripts/ui-jp.js',
            'app/app.js'

        ], elixir.config.publicPath + 'js/app.js');

        // .version(['css/app.css', 'css/vendor.css', 'js/vendor.js']);
});