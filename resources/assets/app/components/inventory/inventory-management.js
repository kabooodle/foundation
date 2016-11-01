import store from './manage/store';
import computed from './manage/computed';
import StyleTemplate from './style_template.vue';

new Vue({
    el: '#manage_inventory',
    store,
    computed,

    beforeCreate () {
        $Bus.$emit('loader:request-show');
    },

    created : function() {
        this.$store.dispatch('setRoute', inventory_route);

        this.getInventory();
        this.getPostables();
        const scope = this;

        $Bus.$on('post-menu:closed', function(){
            // this.facebook_album_selected = false;
            // $('[name=facebookalbums]:checked').prop('checked', false);
        });

        $Bus.$on('facebook-album:changed', function(){
            let album_items = scope.selected.fb_album.items;
            if (album_items && album_items.length > 0) {
                scope.$store.commit('SET_SELECTED_ITEMS', scope.selected.fb_album.items);
            }
        });

        $Bus.$on('facebook-group:changed', function(){
            // scope.postables.facebookgroups = _.map(scope.postables.facebookgroups, function(group){
            //     group.albums = _.map(group.albums, function(album){
            //         if (album.hasOwnProperty('items') && album.items.length > 0){
            //             album.items = [];
            //         }
            //         return album;
            //     });
            //     return group;
            // });
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
                let selected_items = this.$store.getters.getSelectedItems;
                if (this.selected.fb_album && selected_items.length > 0){
                    this.assignItemsToSelectedAlbum(selected_items);
                    this.sums.selected_postables = _.chain(this.selected.postables.fb_albums)
                        .filter(function(album){
                            return album.hasOwnProperty('items') && album.items.length > 0;
                        })
                        .reduce(function(k,v){
                            return k+v.items.length;
                        }, 0)
                        .value();

                    // return;
                }
                // this.resetData('sums.selected_postables');
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
            // We need to reset the selected group and album
            // to prevent residual propogation between selections
            this.$store.commit('RESET_SELECTED_FB_GROUP');
            this.$store.commit('RESET_SELECTED_FB_ALBUM');
            if(groupId && groupId != '') {
                this.selected.fb_group = this.getFacebookGroup(groupId);
            } else {
                this.selected.fb_group = '';
            }
            this.selected.items = [];
            $Bus.$emit('facebook-group:changed');
        },
        getFacebookGroup : function(id) {
            let facebookgroups = this.postables.facebookgroups;
            if(facebookgroups) {
                return _.findWhere(facebookgroups, {id : id});
            }
            return null;
        },
        assignItemsToSelectedAlbum: function(items) {
            var index = this.selectedPostables.fb_albums.indexOf(this.selected.fb_album);
            this.selectedPostables.fb_albums[index].items = items;
        },
        removeFromAlbum : function(fitem, facebook, event) {
            let index = facebook.items.indexOf(fitem);
            if (index > -1){
                facebook.items.splice(index, 1);
            }
        },
        selectFacebookAlbum : function(facebook, event){
            this.selected.fb_album = facebook;
            if(this.selectedPostables.fb_albums.indexOf(facebook) == -1) {
                this.selectedPostables.fb_albums.push(facebook);
            }
            // this.selected.items = [];
            this.$store.commit('RESET_SELECTED_ITEMS');
            $Bus.$emit('facebook-album:changed');
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
        selectAllInventory : function(event){
            event.preventDefault();
            $Bus.$emit('inventory:select-all');
        },
        resetInventory : function() {
            this.selected.items = [];
            this.closePostMenu();
            $Bus.$emit('inventory:request-reset');
        },
        getInventory : function() {
            let scope = this;
            this.actions.refreshing_data = true;
            this.inventory_items = [];
            this.resetInventory();
            scope.$emit('refreshing_data');

            this.$http.get(this.$store.getters.getInventoryRoute).then(function(response){
                scope.$store.commit('SET_INVENTORY_ITEMS', response.body.data);
            }).then(function(){
                // scope.resetData('actions.refreshing_data');
                scope.actions.refreshing_data = false;
            });
        },
        getPostables : function() {
            let scope = this;

            this.$http.get('http://'+window.location.hostname+'/inventory/postables').then(function(response){
                scope.$store.commit('SET_POSTABLES', response.body.data);
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