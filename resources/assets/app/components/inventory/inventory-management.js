import store from './manage/store';
import computed from './manage/computed';
import StyleTemplate from './style_template.vue';
import FacebookLogin from '../facebook/FacebookLogin.vue';

new Vue({
    el: '#manage_inventory',
    store,
    computed,
    created(){
        this.$store.dispatch('setRoute', inventory_route);

        this.getInventory();
        this.getPostables();

        $Bus.$on('facebook:refreshing', (status)=>{
            $('#post_facebook_group_el option').prop("selected", false);
            $('#post_facebook_group_el').val([]).trigger('change');
            this.resetSelectedFBAlbum();
            this.resetSelectedFBGroup();
            this.actions.getting_postables = true;
        });

        $Bus.$on('facebook:refreshed', (fbAuth, postables)=>{
            this.postables = postables;
            this.actions.getting_postables = false;
        });

        $Bus.$on('post-menu:closed', ()=>{
            // this.facebook_album_selected = false;
            // $('[name=facebookalbums]:checked').prop('checked', false);
        });

        $Bus.$on('facebook-album:changed', ()=>{
            let album_items = this.selected.fb_album.items;
            if (album_items && album_items.length > 0) {
                this.$store.commit('SET_SELECTED_ITEMS', this.selected.fb_album.items);
            }
        });

        $Bus.$on('facebook-group:changed', ()=>{
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
            handler(v){
                // v.refreshing_data === false ? $Bus.$emit('loader:request-close') : $Bus.$emit('loader:request-show');
            }, deep: true
        },
        selected : {
            handler(selected){
                // if we currently have a fb album selected and items selected
                // assign the inventory items to the selected album.
                // Then, sum the selected items and add it to the the sum var.
                let selected_items = this.$store.getters.getSelectedItems;
                if (this.selected.fb_album && selected_items.length > 0){
                    this.assignItemsToSelectedAlbum(selected_items);
                    this.sums.selected_postables = _.chain(this.selected.postables.fb_albums)
                        .filter((album)=>{
                            return album.hasOwnProperty('items') && album.items.length > 0;
                        })
                        .reduce((k,v)=>{
                            return k+v.items.length;
                        }, 0)
                        .value();
                }
            }, deep: true
        }
    },
    methods: {
        // Reset
        resetState(){
            this.$store.commit('CLEAR_READER_STATE');
        },
        resetSelectedItems(){
            this.$store.commit('RESET_SELECTED_ITEMS');
        },
        resetSelectedPostables(){
            this.$store.commit('RESET_SELECTED_POSTABLES');
        },
        resetSelectedFBGroup(){
            this.$store.commit('RESET_SELECTED_FB_GROUP');
        },
        resetSelectedFBAlbum(){
            this.$store.commit('RESET_SELECTED_FB_ALBUM');
        },

        // Called when changing the facebook group radio
        changeFacebookGroup(event){
            let groupId = event.target.value;
            // We need to reset the selected group and album
            // to prevent residual propagation between selections
            this.resetSelectedFBAlbum();
            this.resetSelectedFBGroup();

            // Set the selected facebook group.
            if(groupId && groupId != '') {
                this.selected.fb_group = this.getFacebookGroup(groupId);
            } else {
                this.selected.fb_group = '';
            }

            // Reset selected items to prevent confusing shit.
            this.selected.items = [];

            // Tell the world.
            $Bus.$emit('facebook-group:changed');
        },
        getFacebookGroup(id){
            let facebookgroups = this.postables.facebookgroups;
            if(facebookgroups) {
                return _.findWhere(facebookgroups, {id : id});
            }
            return null;
        },

        // Assign inventory items to the selected FB album
        assignItemsToSelectedAlbum(items){
            var index = this.selected.postables.fb_albums.indexOf(this.selected.fb_album);
            this.selected.postables.fb_albums[index].items = items;
        },

        // Remove an inventory item from a fb album
        removeFromAlbum(fitem, facebook, event){
            let index = facebook.items.indexOf(fitem);
            if (index > -1){
                facebook.items.splice(index, 1);
            }
        },

        // Set the selected facebook album.
        //
        selectFacebookAlbum(facebook, event){
            // Bugfix: filter through the selected fb albums and if they dont have items assigned to them
            // remove them from the selected list.
            this.selected.postables.fb_albums = _.filter(this.selected.postables.fb_albums, function(album){
                return album.hasOwnProperty('items') && album.items.length > 0;
            });

            this.selected.fb_album = facebook;

            // Confirm the fb album doesn't already exist in the array.
            if(this.selected.postables.fb_albums.indexOf(facebook) == -1) {
                this.selected.postables.fb_albums.push(facebook);
            }
            // this.selected.items = [];
            this.resetSelectedItems();

            $Bus.$emit('facebook-album:changed');
        },

        setPostingToSales(val){
            this.actions.posting_to_sales = val;
        },

        // Opens the posts sidebar menu
        openPostMenu(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).toggleClass('reveal');

            this.$emit('post-menu:opened');
        },

        // Closes the posts sidebar menu
        closePostMenu(event){
            $('#navbarSide').css({
                'top' :  $('.app-header').outerHeight()
            }).removeClass('reveal');

            this.$emit('post-menu:closed');
        },

        // Method for performing an AJAX post request
        // of the selected flash sales, facebook album items
        postSelectedItemsToSales(event){
            event.preventDefault();
            let $el = $(event.target);
            let form = $el.closest('form');
            const selectedPostables = this.selected.postables;
            selectedPostables.fb_group = this.selected.fb_group;
            selectedPostables.options = {};
            selectedPostables.options.ends_at = form.find('[name="options[ends_at]"]').val();
            selectedPostables.options.available_at = form.find('[name="options[available_at]"]').val();
            selectedPostables.options.include_text = form.find('[name="options[include_text]"]').is(':checked') ? 1 : 0;
            selectedPostables.items = this.selected.items;

            // Perhaps fire event instead of calling method directly.
            this.setPostingToSales(true);

            this.$http.post(form.prop('action'), selectedPostables).then((response)=>{
                $('[name="facebook_group"] option').prop("selected", false)
                this.selected.items = [];
                this.resetSelectedPostables();
                this.resetSelectedFBGroup();
                this.resetSelectedFBAlbum();
                notify({text: response.body.data.msg, type : 'success'});
            }, (response)=>{
                notify({text: response.body.data.msg});
            }).finally(()=>{
                this.setPostingToSales(false);
            });
        },
        toggleFlashSale(i, event){
            let el = event.target;
            let isChecked = el.checked;
            if (isChecked) {
                this.selected.postables.flashsales = i;
            }
        },
        selectAllInventory(event){
            event.preventDefault();

            $Bus.$emit('inventory:select-all');
        },
        resetInventory(){
            this.selected.items = [];
            this.closePostMenu();

            $Bus.$emit('inventory:request-reset');
        },
        getInventory(){
            this.actions.refreshing_data = true;
            this.inventory_items = [];
            this.resetInventory();
            this.$emit('refreshing_data');

            this.$http.get(this.$store.getters.getInventoryRoute).then((response)=>{
                // Called using computed property;
                this.inventory_items = response.body.data;
            }).then(()=>{
                // scope.resetData('actions.refreshing_data');
                this.actions.refreshing_data = false;
            });
        },
        getPostables(){
            const that = this;
            this.actions.getting_postables = true;
            this.$http.get(window.location.protocol+'//'+window.location.hostname+'/inventory/postables').then((response)=>{
                // Called using computed property;
                that.postables = response.body.data;
                that.actions.getting_postables = false;
            }, (response)=>{
                that.actions.getting_postables = false;
            });
        },
        toggleIncludeLinkOption() {
            let wrapper = $('#wrapper-if-include-link');
            if(wrapper.is(':visible')) {
                wrapper.hide();
                wrapper.find('input').val('');
            } else {
                wrapper.show();
            }
        },
        clearDateTimeInput(elname){
            $('#'+elname).val('');
        }
    },
    components: {
        'style-template' : StyleTemplate,
        'facebook-login' : FacebookLogin
    }
});