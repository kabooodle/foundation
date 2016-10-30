global.$ = global.jQuery = require('jquery');
global.Vue = require('vue/dist/vue.js');
require('vue-resource');
// require('vue-validator');
global.Tether = require('tether');
require('bootstrap');
global._ = require('underscore');
require('moment');
require('moment-timezone-tsc');

// Use localized version of Noty because I've heavily modified it :)
require('../../../resources/assets/vendor/jquery/noty/packaged/jquery.noty.packaged.js');

global.Clipboard = require('clipboard');
require('ekko-lightbox');
require('selectize');
require('emojione');
require('emojionearea');
require('bootstrap-touchspin');
global.datetimepicker = require('eonasdan-bootstrap-datetimepicker');
//
// // Use the localized version because the NPM version is being fuckity.
global.multiselect = require('../vendor/bootstrap-multiselect/dist/js/bootstrap-multiselect');

// Vue.config.devtools = false;

global.$Bus = new Vue();