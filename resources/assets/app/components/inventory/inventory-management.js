import StyleTemplate from './style_template.vue';

function getDefaultData(key) {
    let data = {

        // Array of user inventory items
        inventory_items: [],

        // Array of postable objects (flash sales, facebook groups)
        postables : {
            facebookgroups: [],
            flashsales: []
        },

        selected: {
            // Holds sizes that have been selected
            sizes: [],

            // Holds items that have been selected
            items: [],

            // Holds postables that have been selected
            postables: {
                fb_albums: [],
                flashsales: []
            },

            // Toggle of the selected fB group
            fb_group: null,

            // Toggle of the selected fb album
            fb_album: null
        },

        actions : {
            refreshing_data: false,
            posting_to_sales: false,
            fb_advanced_menu: false
        },

        sums : {
            selected_postables: 0
        },

        selected_sales_sum : 0,
    };

    if(key) {
        let keys = key.split('.');
        if(keys.length) {
            // data is only 2 deep max, so, hardcode it.
            return keys.length > 1 ? data[keys[0]][keys[1]] : data[key];
        }
    }

    return data;
}


new Vue({
    el: '#manage_inventory',
    data : getDefaultData,
    computed : {

    },
    created : function() {
        this.getInventory();
        this.getPostables();
        this.$on('post-menu:closed', function(){
            // this.facebook_album_selected = false;
            // $('[name=facebookalbums]:checked').prop('checked', false);
        });
    },
    watch: {
        'actions' : {
            handler: function(v) {
                v.refreshing_data === false ? $Bus.$emit('loader:request-close') : $Bus.$emit('loader:request-show');
            }, deep: true
        },
        // facebooks : {
        //     handler: function() {
        //         const scope = this;
        //         let sum = 0;
        //         $.each(this.facebooks, function(){
        //             sum += this.fitems.length;
        //         });
        //         scope.selected_sales_sum = sum;
        //     }, deep : true
        // },
        selected : {
            handler: function(selected){
                let selected_items = selected.items;
                if (selected.fb_album && selected_items.length > 0){
                    this.assignItemsToSelectedAlbum(selected_items);
                    this.sums.selected_postables = _.chain(selected.postables.fb_albums)
                        .filter(function(album){
                            return album.hasOwnProperty('items') && album.items.length > 0;
                        })
                        .reduce(function(k,v){
                            return k+v.items.length;
                        }, 0)
                        .value();

                    return;
                }
                this.resetData('sums.selected_postables');
            }, deep: true
        }
    },
    methods: {
        resetData: function (key) {
            if(key) {
                let keys = key.split('.');
                return keys.length > 1 ? this[keys[0]][keys[1]] = getDefaultData(key) : this[key] = getDefaultData(key);
            } else {
                this.$data = getDefaultData();
            }
        },
        changeFacebookGroup : function(event){
            let groupId = event.target.value;
            if(groupId && groupId != '') {
                this.selected.fb_group = this.getFacebookGroup(groupId);
            } else {
                this.resetData('selected.fb_group')
            }
            $Bus.$emit('facebook-selection:changed');
            this.resetData('selected.items');
        },
        getFacebookGroup : function(id) {
            let facebookgroups = this.postables.facebookgroups;
            if(facebookgroups) {
                return _.findWhere(facebookgroups, {id : id});
            }
            return null;
        },
        assignItemsToSelectedAlbum: function(items) {
            var index = this.selected.postables.fb_albums.indexOf(this.selected.fb_album);
            this.selected.postables.fb_albums[index].items = items;
        },
        removeFromAlbum : function(fitem, facebook, event) {
            let index = facebook.items.indexOf(fitem);
            if (index > -1){
                // this.$nextTick(function(){
                    facebook.items.splice(index, 1);
                // })
            }
        },
        selectFacebookAlbum : function(facebook, event){
            this.selected.fb_album = facebook;
            if(this.selected.postables.fb_albums.indexOf(facebook) == -1) {
                this.selected.postables.fb_albums.push(facebook);
            }
            $Bus.$emit('facebook-selection:changed');
            this.resetData('selected.items');
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
            let scope = this;
            let $el = $(event.target);
            let form = $el.closest('form#post_sales_form');
            let form_data = new FormData();
            let selected_items_ids = _.pluck(this.selected_items, 'id');

            $.each(form.serializeArray(), function(){
                form_data.append(this.name, this.value);
            });

            for (let i = 0; i < selected_items_ids.length; i++) {
                form_data.append('selected_items_ids[]', selected_items_ids[i]);
            }

            // Perhaps fire event instead of calling method directly.
            this.setPostingToSales(true);

            this.$http.post(form.prop('action'), form_data).then(function(response){
                notify({'text' : selected_items_ids.length+' Items posted or queued successfully', 'type' : 'success'});
            }, function(response){
                //
            }).finally(function(){
                scope.setPostingToSales(false);
            });
        },
        toggleFlashSale : function(i, event) {
            let el = event.target;
            let isChecked = el.checked;
            let index = this.selected_sales.flashsales.indexOf(i);
            if (isChecked) {
                this.selected_sales.flashsales.push(i);
            } else {
                if (index > -1) {
                    this.selected_sales.flashsales.splice(index, 1);
                }
            }
        },
        resetInventory : function() {
            this.resetData('selected.items');
            this.closePostMenu();
            $Bus.$emit('inventory:request-reset');
        },
        getInventory : function() {
            let scope = this;
            this.actions.refreshing_data = true;
            scope.resetData('inventory_items');
            this.resetInventory();
            scope.$emit('refreshing_data');

            this.$http.get('http://api.kabooodle.dev/inventory/jaketoolson').then(function(response){
                scope.inventory_items = response.body.data
            }).then(function(){
                scope.resetData('actions.refreshing_data');
            });
        },
        getPostables : function() {
            let scope = this;

            this.$http.get('http://app.kabooodle.dev/inventory/postables').then(function(response){
                scope.postables = response.body.data;
            }, function(response){
                console.log('Error in retrieving postables');
            });
        },
        clearDateTimeInput: function() {
            $('#datetimepicker2').val('');
        }
    },
    components: {
        'style-template' : StyleTemplate
    }
});