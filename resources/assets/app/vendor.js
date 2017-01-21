global.$ = global.jQuery = require('jquery');
global.Vue = require('vue/dist/vue.common.js');
global.VueResource = require('vue-resource');
global.Tether = require('tether');
require('bootstrap');
global._ = require('underscore');
global.moment = require('moment');
require('moment-timezone-tsc');
global.slider = require('bootstrap-slider');
// Use localized version of Noty because I've heavily modified it :)
require('../../../resources/assets/vendor/jquery/noty/packaged/jquery.noty.packaged.js');

global.Clipboard = require('clipboard');
require('ekko-lightbox');
// require('selectize'); // Removed
require('select2');
require('emojione');
require('emojionearea');
require('bootstrap-touchspin');
global.datetimepicker = require('eonasdan-bootstrap-datetimepicker');
require('../vendor/tablesaw/tablesaw');
require('perfect-scrollbar/jquery')($);
require('bootstrap-select');
//
// // Use the localized version because the NPM version is being fuckity.
global.multiselect = require('../vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect');

import VueTimeago from 'vue-timeago';

Vue.use(VueResource);
Vue.use(VueTimeago, {
    name: 'timeago', // component name, `timeago` by default
    locale: 'en-US',
    locales: {
        'en-US': require('vue-timeago/locales/en-US.json')
    }
});

global.$Bus = new Vue();

// Vue.config.devtools = false;