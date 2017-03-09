global.$ = global.jQuery = require('jquery');
global.Vue = require('vue/dist/vue.common.js');
global.VueResource = require('vue-resource');
global.Tether = require('tether');
require('bootstrap');
global._ = require('lodash');
global.slider = require('bootstrap-slider');
global.moment = require('moment-timezone');

require('../../../resources/assets/vendor/jquery/noty/packaged/jquery.noty.packaged.js');

global.Clipboard = require('clipboard');
require('ekko-lightbox');
require('select2');
require('emojione');
require('emojionearea');
require('bootstrap-touchspin');
require('../vendor/tablesaw/tablesaw');
require('perfect-scrollbar/jquery')($);

global.multiselect = require('../vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect');
global.datetimepicker = require('../vendor/bootstrap-datetimepicker/bootstrap-datetimepicker');
// global.selectpicker = require('../vendor/bootstrap-select/bootstrap-select');

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
