// var Vue = require('vue/dist/vue.js');
import PopOver from '../popover.vue';
import StyleTemplate from './style_template.vue';
import Loading from '../loading.vue';

new Vue({
    el: '#manage_inventory',
    data : {
        selected_sales_sum : 0,
        selected_sales : {
            flashsales : [],
            facebook : []
        },
        facebooks : [
            {
                id: 1,
                name: '#1',
                fitems: []
            },
            {
                id: 2,
                name: '#2',
                fitems: []
            },
            {
                id: 3,
                name: '#3',
                fitems: []
            },
            {
                id: 4,
                name: '#4',
                fitems: []
            },
            {
                id: 5,
                name: '#5',
                fitems: []
            },
        ],
        date_time_input : null,
        selected_items: [],
        data_items : [],
        refreshing_data: false,
        posting_to_sales: false,
        facebook_album_selected : null
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
    created : function() {
        this.getInventory();
        this.$on('post-menu:closed', function(){
            // this.facebook_album_selected = false;
            // $('[name=facebookalbums]:checked').prop('checked', false);
        });
    },
    watch: {
        refreshing_data : {
            handler: function(v) {
                console.log(v);
                if (v == false) {
                    $Bus.$emit('loader:request-close');
                } else {
                    $Bus.$emit('loader:request-show');
                }
            }
        },
        facebooks : {
            handler: function($object, $m) {
                const scope = this;
                var sum = 0;
                $.each(this.facebooks, function(){
                    sum += this.fitems.length;
                });
                scope.selected_sales_sum = sum;
            }, deep : true
        },
        selected_items : {
            handler: function(selected_items){
                if (this.facebook_album_selected && selected_items.length > 0){
                    this.assignItemsToSelectedAlbum(selected_items);
                }
            }
        }
    },
    methods: {
        assignItemsToSelectedAlbum(items) {
            const scope = this;
            // might consider using debounce
            setTimeout(function(){
                scope.facebook_album_selected.fitems = items;
            }, 100);
        },
        removeFromAlbum(fitem, facebook, event) {
            var index = facebook.fitems.indexOf(fitem);
            if (index > -1){
                facebook.fitems.splice(index, 1);
            }
        },
        selectFacebookAlbum : function(facebook, event){
            this.facebook_album_selected = facebook;
            this.selected_items = [];
        },
        setPostingToSales : function(val){
            this.posting_to_sales = val;
        },
        openPostMenu : function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal');

            this.$emit('post-menu:opened');
        },
        closePostMenu : function(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).removeClass('reveal');

            this.$emit('post-menu:closed');
        },
        postSelectedItemsToSales : function(event) {
            event.preventDefault();
            var scope = this;
            var $el = $(event.target);
            var form = $el.closest('form#post_sales_form');
            var form_data = new FormData();
            var selected_items_ids = _.pluck(this.selected_items, 'id');

            $.each(form.serializeArray(), function(){
                form_data.append(this.name, this.value);
            });

            for (var i = 0; i < selected_items_ids.length; i++) {
                form_data.append('selected_items_ids[]', selected_items_ids[i]);
            }

            // Perhaps fire event instead of calling method directly.
            this.setPostingToSales(true);

            this.$http.post(form.prop('action'), form_data).then(function(response){
                notify({'text' : selected_items_ids.length+' Items posted or queued successfully', 'type' : 'success'});
                scope.selected_items = [];
                scope.selected_sales =  {
                    flashsales : [],
                    facebook : []
                };
            }, function(response){
                //
            }).finally(function(){
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

            this.$http.get('http://api.kabooodle.dev/inventory/jaketoolson').then(function(response){
                scope.data_items = response.body.data
            }).then(function(){
                scope.refreshing_data = false;
            });
        },
        clearDateTimeInput: function() {
            $('#datetimepicker2').val('');
        }
    },
    components: {
        'style-template' : StyleTemplate,
        'popout-overlay' : PopOver,
        'loading' : Loading
    }
});