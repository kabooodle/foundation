global.$ = global.jQuery = require('jquery');
global.Vue = require('vue/dist/vue.common.js');
global.VueResource = require('vue-resource');
global.Tether = require('tether');
require('bootstrap');
require('bootstrap-select');
global._ = require('lodash');
global.moment = require('moment-timezone');
global.slider = require('bootstrap-slider');

require('../../../resources/assets/vendor/jquery/noty/packaged/jquery.noty.packaged.js');

global.Clipboard = require('clipboard');
require('ekko-lightbox');
require('select2');
require('emojione');
require('emojionearea');
require('bootstrap-touchspin');
global.datetimepicker = require('eonasdan-bootstrap-datetimepicker');
require('../vendor/tablesaw/tablesaw');
require('perfect-scrollbar/jquery')($);

global.multiselect = require('../vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect');

import VueTimeago from 'vue-timeago';

Vue.use(VueResource);
Vue.use(VueTimeago, {
    name: 'timeago',
    locale: 'en-US',
    locales: {
        'en-US': require('vue-timeago/locales/en-US.json')
    }
});

global.$Bus = new Vue();
