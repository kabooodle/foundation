// var Vue = require('vue/dist/vue.js');
import PopOver from '../popover.vue';
import StyleTemplate from './style_template.vue';

new Vue({
    el: '#manage_inventory',
    data : {
        selected_sales : {
            flashsales : [],
            facebook : []
        },
        selected_items: [],
        data_items : [],
        refreshing_data: false,
        posting_to_sales: false,
    },
    computed : {
        get_selected_facebook_sales : function() {
            return this.selected_sales.facebook;
        },
        get_selected_flashsales_sales : function() {
            return this.selected_sales.flashsales;
        },
        sum_selected_sales : function() {
            return this.get_selected_flashsales_sales.length + this.get_selected_facebook_sales.length
        },
        sum_selected_sales_and_selected_items : function() {
            return this.selected_items.length + this.sum_selected_sales;
        },
        has_selected_sales_and_selected_items : function() {
            return !(this.selected_items.length == 0 || this.sum_selected_sales == 0);
        },
    },
    mounted : function() {
        this.getInventory();
    },
    methods: {
        setPostingToSales : function(val){
            this.posting_to_sales = val;
        },
        openPostMenu : function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal');
        },
        closePostMenu : function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).removeClass('reveal');
        },
        postSelectedItemsToSales : function(event) {
            event.preventDefault();
            var scope = this;
            var $el = $(event.target);
            var form = $el.closest('form#post_sales_form');
            var form_data = form.serializeArray();

            // Perhaps fire event instead of calling method directly.
            this.setPostingToSales(true);

            form_data.push({
                'name' : 'selected_items_ids[]',
                'value' :  _.pluck(this.selected_items, 'id')
            });
            
            this.$http.post('http://api.kabooodle.dev/inventory/jaketoolson/associate', form_data).then(function(response){

            }, function(response){
                //
            }).then(function(){
                scope.setPostingToSales(false);
            });
        },
        toggleFlashSale : function(i, event) {
            var el = event.target;
            var isChecked = el.checked;
            var index = this.selected_sales.flashsales.indexOf(i);
            if (isChecked) {
                this.selected_sales.flashsales.push(i);
            } else {
                if (index > -1) {
                    this.selected_sales.flashsales.splice(index, 1);
                }
            }
        },
        resetInventory : function() {
            this.selected_items = [];
            this.selected_sales =  {
                flashsales : [],
                facebook : []
            };
            this.posting_to_sales = false;
            this.closePostMenu();
            $Bus.$emit('inventory:request-reset');
        },
        getInventory : function() {
            var scope = this;
            this.refreshing_data = true;
            this.data_items = [];
            this.resetInventory();
            scope.$emit('refreshing_data');
            $.ajax({
                method: 'GET',
                url : window.location.href
            }).success(function(response){
                scope.data_items = response;
            }).always(function(){
                scope.refreshing_data = false;
            });
        }
    },
    components: {
        'style-template' : StyleTemplate,
        'popout-overlay' : PopOver
    }
});