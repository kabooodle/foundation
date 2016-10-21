global.$ = global.jQuery = require ('jquery');
global.Vue = require('vue/dist/vue.js');
require ('vue-resource');
// require ('vue-validator');
global.Tether = require ('tether');
require ('bootstrap');
global._ = require ('underscore');
require ('moment');
require ('moment-timezone-tsc');

// require ('noty');
require('../../../resources/assets/vendor/jquery/noty/packaged/jquery.noty.packaged.js');

require ('clipboard');
require ('ekko-lightbox');
require ('selectize');
require ('emojione');
require ('emojionearea');
require ('bootstrap-touchspin');
$.multiselect = require ('bootstrap-multiselect');
require ('eonasdan-bootstrap-datetimepicker');

global.$Bus = new Vue();