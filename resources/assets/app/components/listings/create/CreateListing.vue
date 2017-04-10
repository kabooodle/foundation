<template>
    <div>

        <div class="box white">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-group">
                            <div class="card no-border text-center">
                                <img :style="selected.step !== 1 ?  'opacity: .4' : null" class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/inventory.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 1 ?  'text-muted' : '_600'"
                                            class="m-b-0 card-title">
                                        Select Inventory
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.listables.length }} selected</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <img :style="selected.step !== 2 ?  'opacity: .4' : null" class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/sell_and_display.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 2 ?  'text-muted' : '_600'"
                                            class="m-b-0 text-center card-title">Select Sale
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.sales.length }} sales prepared</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <img :style="selected.step !== 3 ?  'opacity: .4' : null" class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/preview.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 3 ?  'text-muted' : '_600'"
                                            class="m-b-0 text-center card-title">Preview
                                        <span class="block text-xs text-muted m-t-sm">Make changes, add options</span>
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box white">

            <steps
                :current_step="selected.step"
                :selected_listables_count="selected.listables.length"
                :selected_sales_count="selected.sales.length"
            ></steps>

            <div class="box-body">
                <template v-if="selected.step == 1">
                    <listable-groups
                            :show_select_buttons="show_select_buttons"
                            :listablegroupings_endpoint="endpoint"
                            :display_footer_buttons="false"
                            :initial-disable-unavailable="true"
                    ></listable-groups>
                </template>

                <template v-if="selected.step == 2">
                    <spinny class="text-center center-block" v-if="actions.refreshing_postables"></spinny>
                    <div v-else class="row">
                        <div class="col-md-6 col-md-offset-3">
                            <label class="control-label">Select sale type</label>
                            <div class="card-group btn-group-prpl">
                                <div class="card no-border">
                                    <button
                                            :class="postables.facebookgroups.length == 0 ? 'disabled' : (selected.sale.sale_type == 'facebook' ? 'active' : null)"
                                            :disabled="postables.facebookgroups.length == 0 "
                                            @click="selectSale('facebook')"
                                            class="btn text-center white btn-lg btn-block">
                                        Facebook
                                        <span class="text-sm text-muted block">{{postables.facebookgroups.length}} groups</span>
                                    </button>
                                </div>
                                <div class="card no-border">
                                    <button
                                            :class="postables.flashsales.length == 0 ? 'disabled' : (selected.sale.sale_type == 'flashsale' ? 'active' : null)"
                                            :disabled="postables.flashsales.length == 0 "
                                            @click="selectSale('flashsale')"
                                            class="btn text-center white btn-lg btn-block">
                                        Flash Sale
                                        <span class="text-sm text-muted block">{{postables.flashsales.length}} sales</span>
                                    </button>
                                </div>
                            </div>
                            <div class="clearfix">
                                <facebook-login
                                        class="pull-left"
                                        :refresh_endpoint="facebook_refresh_endpoint"
                                ></facebook-login>
                            </div>

                            <template v-if="selected.sale">
                                <div class="form-group m-t-1" v-if="selected.sale.sale_type == 'flashsale'">
                                    <div class="box white m-t-1" v-if="selected.sale">
                                        <div class="box-body">
                                            <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                                <label>
                                                    <input
                                                            :checked="toggle_albums"
                                                            @click="toggleSalesItems($event)"
                                                            data-type="albums-associated-toggler"
                                                            type="checkbox" />
                                                    <span class="text-sm">Toggle all albums with associated items</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="control-label">Select sale(s)</label>
                                    <div class="m-b-sm" v-for="sale in postables.flashsales" >
                                        <label class="form-check-label block md-check">
                                            <input
                                                    :data-sale-id="sale.id"
                                                    :checked="_.findIndex(selected.sales, {sale_id: sale.id}) > -1"
                                                    key="sale.id"
                                                    :value="selected.sale.sale_id"
                                                    :name="sale.id"
                                                    @click="selectFlashsale(sale, $event)"
                                                    class="form-check-input"
                                                    type="checkbox">
                                            <i class="indigo"></i>
                                            <span
                                                    :class="_.findIndex(selected.sales, {sale_id: sale.id}) > -1 ? 'text-primary' : 'text-muted'"
                                            >{{ sale.name }}</span>
                                        </label>
                                        <template v-if="_.findIndex(selected.sales, {sale_id: sale.id}) > -1">
                                            <div class="m-t-0">
                                                <span class="text-xs text-muted m-l-2">{{ selected.sales[_.findIndex(selected.sales, {sale_id: sale.id})].listables.length }} items associated. <a href="javascript:;" class="text-primary">View</a></span>
                                                <div
                                                        class="m-l-2 m-t-1" >
                                                    <div class="inline m-r-sm"
                                                         v-for="listable in selected.sales[_.findIndex(selected.sales, {sale_id: sale.id})].listables"
                                                         :key="listable.id"
                                                         :style="! toggle_albums ? 'display: none;' : null"
                                                    >
                                                        <span
                                                                class="avatar_container _48 avatar-thumbnail">
                                                              <img :src="listable.cover_photo">
                                                        </span>
                                                        <div class="block" style="margin: -6px 2px 0 0">
                                                            <span class="text-right" @click="removeItemFromFlashsale(listable, sale.id, $event)" ><i class="fa fa-times text-danger pointer"></i></span>
                                                        </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <template v-if="selected.sale.sale_type == 'facebook'">
                                    <div class="form-group m-t-1">
                                        <label class="control-label">Select group</label>
                                        <select @change="selectFacebookGroup" class="form-control">
                                            <option value="-1"></option>
                                            <option
                                                    v-for="fbgroup in postables.facebookgroups"
                                                    :selected="selected.sale.sale_id == fbgroup.id"
                                                    :value="fbgroup.id">{{ fbgroup.name }}</option>
                                        </select>
                                    </div>
                                    <div class="box white m-t-1 m-b-1" v-if="selected.sale.sale_id">
                                        <div class="box-body">
                                            <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                                <label>
                                                    <input
                                                            :checked="selected.sale.magical_matcher"
                                                            @click="matchInventory($event)"
                                                            data-type="magic-toggler"
                                                            type="checkbox" />
                                                    <span class="text-sm">Automatically match inventory to albums</span>
                                                </label>
                                            </div>
                                            <div class="text-sm" v-if="selected.sale.magical_matcher">
                                                <p class="m-b-0 info text-center r p-sm m-t-sm">{{ display_matches_text }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box white m-t-1" v-if="selected.sale.sale_id">
                                        <div class="box-body">
                                            <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                                <label>
                                                    <input
                                                            :checked="toggle_albums"
                                                            @click="toggleSalesItems($event)"
                                                            data-type="albums-associated-toggler"
                                                            type="checkbox" />
                                                    <span class="text-sm">Toggle all albums with associated items</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group m-t-1" v-if="selected.sale.sale_id">
                                        <div class="box white m-t-1" v-if="selected.sale.sale_id">
                                            <div class="box-header">
                                                <label class="control-label">{{ notMatchedListablesText }}</label>
                                                <a
                                                    v-show="selected.listables.length"
                                                    v-html="showToBeMatched ? 'Hide' : 'View'"
                                                    href="javascript:;"
                                                    @click.prevent="showToBeMatched = !showToBeMatched"
                                                    class="text-primary text-sm">Hide</a>
                                            </div>
                                            <div class="box-body" v-show="showToBeMatched">
                                                <draggable
                                                    id="to-be-matched"
                                                    class="drag-area"
                                                    v-model="selected.listables"
                                                    :options="{group: 'listables'}"
                                                >
                                                    <listable
                                                        v-for="listable in selected.listables" :key="listable.id"
                                                        :listable="listable"
                                                        v-on:remove-item="removeItemFromSelected(listable)"
                                                    ></listable>
                                                </draggable>
                                            </div>
                                        </div>
                                        <div class="m-b-sm album" v-for="album in postables.facebookgroups[_.findIndex(postables.facebookgroups, {id: selected.sale.sale_id})].albums" >
                                            <label class="form-check-label block md-check">
                                                <input
                                                    :data-album-id="album.id"
                                                    :checked="album.listables.length"
                                                    key="album.id"
                                                    :value="selected.sale.sale_id"
                                                    :name="album.id"
                                                    @click="selectAlbum(album, $event)"
                                                    class="form-check-input"
                                                    type="checkbox">
                                                <i class="indigo"></i>
                                                <span
                                                    :class="album.listables.length ? 'text-primary' : 'text-muted'"
                                                >{{ album.name }}</span>
                                            </label>
                                            <template>
                                                <div class="m-t-0">
                                                    <span
                                                        v-if="album.listables.length"
                                                        class="text-xs text-muted m-l-2">
                                                        {{ album.listables.length }} items associated.
                                                        <a
                                                            v-html="toggle_albums ? 'Hide' : 'View'"
                                                            href="javascript:;"
                                                            @click.prevent="toggleAlbumItems(album.id, $event)"
                                                            class="text-primary">Hide</a>
                                                    </span>
                                                    <draggable
                                                        v-model="album.listables"
                                                        :id="'album_items_'+album.id"
                                                        class="m-l-2 m-t-1 drag-area"
                                                        :style="! toggle_albums ? 'display: none;' : null"
                                                        :options="{group: 'listables'}"
                                                    >
                                                        <listable
                                                            v-for="listable in album.listables" :key="listable.id"
                                                            :listable="listable"
                                                            v-on:remove-item="removeItemFromAlbum(listable, album, $event)"
                                                        ></listable>
                                                    </draggable>
                                                 </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                    </div>
                </template>

                <template v-if="selected.step == 3">
                    <table data-tablesaw-mode="stack" class="tablesaw tablesaw-stack table table-condensed table-as-list white">
                        <thead>
                        <tr>
                            <th>Sale group/name</th>
                            <th>Sales prepared</th>
                            <th>Items listed</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="sale in prepared_sales_for_preview.flashsales" v-if="prepared_sales_for_preview.flashsales.length">
                            <td>{{ sale.name }} </td>
                            <td>1</td>
                            <td>{{ sale.listables.length }}</td>
                        </tr>
                        <tr v-for="sale in prepared_sales_for_preview.facebook" v-if="prepared_sales_for_preview.facebook.length">
                            <td>{{ sale.name }} </td>
                            <td>{{ sale.albums }}</td>
                            <td>{{ sale.listables }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <div class="m-t-3" v-if="prepared_sales_for_preview.facebook.length">
                        <label class="control-label">Optional settings you can apply to the Facebook listings you've prepared above.</label>
                        <listing-settings></listing-settings>
                    </div>
                </template>
            </div>

            <steps
                    :current_step="selected.step"
                    :selected_listables_count="selected.listables.length"
                    :selected_sales_count="selected.sales.length"
            ></steps>
        </div>
    </div>
</template>
<style>
    .avatar_container .fa {
        margin: 0;
        padding: 0;
        cursor: pointer;
    }
    .drag-area {
        min-height: 5px;
    }
    #to-be-matched {
        max-height: 200px;
        overflow: scroll;
    }
</style>
<script>
    const selected_sale_data = function(){
        return {
            magical_matcher: false,
            sale_type: null,
            sale_id: null,
            sale: null,
            album_id: null,
            album: {},
            listables: [],
        }
    };

    const initial_data = function(){
        return {
            prepared_sales_for_preview: {
                facebook: [],
                flashsales: []
            },

            // All available listables for selection.
            listables: [],

            // All available sales
            postables: {
                facebookgroups : [],
                flashsales : []
            },

            actions: {
                refreshing_data : true,
                refreshing_postables: false,
                resetting_sales: false,
                saving: false,
            },
            completed: {
                steps: []
            },
            selected: {

                // Current active step we are on.
                step: null,

                // Contains the active sale configuration being built.
                sale: {},

                // Contains all the sale configurations we've created.
                sales: [],

                // Contains all the listables we've selected in step 1
                // AND have not yet been assigned to an active sale configuration
                listables: [],
            },

            matching_listables: {
                misses: [],
                matches: [],
            },

            toggle_albums: true,
            showToBeMatched: false,
            facebook_listing_options: {}
        }
    };

    import FacebookLogin from '../../facebook/FacebookLogin.vue';
    import ListingSettings from '../../inventory/Listing-Settings.vue';
    import Fuse from 'fuse.js';
    import Steps from './Steps.vue';
    import Spinny from '../../Spinner.vue';
    import draggable from 'vuedraggable';
    import ListablesGroups from '../../listables/ListableGroupings.vue';
    import Listable from './Listable.vue';
    import InventoryToAlbumMatcher from './InventoryToAlbumMatcher';
    export default{
        props: {
            show_select_buttons: {
                default: true,
            },
            save_endpoint: {
                required: true,
                type: String,
            },
            endpoint: {
                type: String
            },
            facebook_refresh_endpoint: {
                type: String,
                required: true
            }
        },
        data(){
            return initial_data();
        },
        created() {
            this.gotoStepOne();

            $Bus.$on('facebook:refreshing', (status)=>{
                this.postables = {
                    facebookgroups : [],
                    flashsales : []
                };

                this.selected.sale = selected_sale_data();
            });

            $Bus.$on('facebook:refreshed', (fbAuth, postables)=>{
                this.postables = postables;
            });

            $Bus.$on('listings:options:get', (options)=>{
                this.facebook_listing_options = options;
            });

            $Bus.$on('listables:fetched', (listables)=>{
                this.listables = listables;
                this.actions.refreshing_data = false;
            });

            $Bus.$on('listings:selected:listables:reset', ()=>{
                this.selected.listables = [];
            });

            $Bus.$on('listable:selected', (group, subgroup, listable)=>{
                const index = _.findIndex(this.selected.listables, {id: listable.id});
                if (index == -1) {
                    this.selected.listables.push(listable);
                }
            });

            $Bus.$on('listable:removed', (groupid, subgroup, listable)=>{
                const index = _.findIndex(this.selected.listables, {id: listable.id});
                if (index > -1) {
                    this.selected.listables.splice(index, 1);
                }
            });

            $Bus.$on('listings:create:sale:selected', ()=>{
                this.matching_listables.matches = [];
                this.matching_listables.misses = [];
            });
        },
        watch: {
            postables: {
                handler: function () {
                    this.resetFacebookAlbumSales();
                },
                deep: true
            }
        },
        methods: {
            toggleSalesItems(e){
                this.toggle_albums = e.target.checked;
            },
            prepareSalesForPreview(){

                this.facebook_listing_options = {};
                this.prepared_sales_for_preview.flashsales = [];
                this.prepared_sales_for_preview.facebook = [];

                // Give me only the FB sales
                let fbsales = _.filter(this.selected.sales, {sale_type: 'facebook'});

                // Give me only the flashsales
                let flashsales = _.filter(this.selected.sales, {sale_type: 'flashsale'});

                if (flashsales.length) {
                    for (let i = 0; i < flashsales.length; i++) {
                        this.prepared_sales_for_preview.flashsales.push(flashsales[i]);
                    }
                }

                if (fbsales.length) {

                    // Group the fbsales by sale_id (or group)
                    let fbsales_by_group = _.groupBy(fbsales, 'sale_id');

                    // iterate over the facebook sales, grouped by "group" and build a new object
                    // that has the albums listed to for the group, as well as total listables used for the group
                    _.each(fbsales_by_group, (sales)=>{

                        let tempsale = {
                            listables: 0,
                            albums: 0,
                            id: sales[0].sale.id, // peek into the array and get this from the first object as all objects have the same sale id/name
                            name: sales[0].sale.name,
                            sales: [],
                        };

                        for (let i=0; i<sales.length; i++){
                            let sale = sales[i];

                            // Push the album to the parent albums array
                            tempsale.albums++;

                            tempsale.listables += sale.listables.length;
                            tempsale.sales.push({
                                album: sale.album,
                                album_id: sale.album_id,
                                listables: sale.listables,
                                sale_id: sale.sale_id,
                                sale_type: sale.sale_type,
                                sale: {
                                    id: sale.sale.id,
                                    name: sale.sale.name
                                }
                            });
                        }

                        // Push our object to the state
                        this.prepared_sales_for_preview.facebook.push(tempsale);
                    });
                }
            },
            removeItemFromSelected(listable){
                const index = _.findIndex(this.selected.listables, listable);
                if (index > -1) {
                    this.selected.listables.splice(index, 1);
                }
            },
            removeItemFromAlbum(listable, album, e){
                const index = _.findIndex(album.listables, listable);
                if (index > -1) {
                    var local_listable = album.listables.splice(index, 1)[0];
                    if (local_listable) {
                        this.addItemBackToSelected(local_listable);
                        this.selected.sale.magical_matcher = false;
                    }
                }
            },
            removeItemFromFlashsale(listable, sale_id, e){
                const index = _.findIndex(this.selected.sales, {sale_id: sale_id});
                if (index > -1) {
                    this.selected.sales[index].listables = _.filter(this.selected.sales[index].listables, (local_listable)=>{
                        return local_listable.id !== listable.id;
                    });

                    // If the number of listables in a sale reaches 0, remove the sale.
                    if (this.selected.sales[index].listables.length == 0) {
                        this.selected.sales.splice(index, 1);
                    }
                }
            },
            addItemToAlbum(listable, album){
                let index = _.findIndex(album.listables, {id: listable.id});
                if (index == -1) {
                    album.listables.push(listable);
                }
            },
            addItemBackToSelected(listable){
                let index = _.findIndex(this.selected.listables, {id: listable.id});
                if (index == -1) {
                    this.selected.listables.push(listable);
                }
            },

            /**
             *
             * @param {integer} albumid
             * @param e
             */
            toggleAlbumItems(albumid, e){
                let $el = $('#album_items_'+albumid);
                let $target = $(e.target);
                if ($el.is(':visible')){
                    $el.hide();
                    $target.html('View');
                } else {
                    $el.show();
                    $target.html('Hide');
                }
            },
            matchInventory(e){
                let target = e.target;
                this.selected.sale.magical_matcher = true;
                this.matching_listables.matches = [];
                this.matching_listables.misses = [];
                if (target.checked) {
                    // Run the tool magic tool!
                    // Run the tool magic tool!
                    // Run the tool magic tool!
                    let matched = this.buildInventoryAlbumMatchings();

                    // Uh oh, something happened during the magic tool.
                    // Uncheck it !ABORT!
                    if (matched === false) {
                        target.checked = false;
                        e.preventDefault();
                        this.selected.sale.magical_matcher = false;
                    }
                } else {
                    this.resetAllSalesForGroup();
                    this.selected.sale.magical_matcher = false;
                }
            },

            resetAllSalesForGroup(){
                this.actions.resetting_sales = true;

                var that = this;
                let index = _.findIndex(this.postables.facebookgroups, {id: this.selected.sale.sale_id});

                if (index > -1) {
                    let group = this.postables.facebookgroups[index];
                    if (group.albums.length) {
                        group.albums.forEach(function (album) {
                            if (album.listables.length) {
                                album.listables.forEach(function (listable) {
                                    that.addItemBackToSelected(listable);
                                });
                                album.listables = [];
                                return;
                            }
                        });
                    }
                }
            },

            buildInventoryAlbumMatchings(){
                if (this.selected.listables.length == 0) {
                    notify({
                        text: 'You must first select inventory.'
                    });

                    return false;
                }

                // possible names we can search against and ignore
                let ignored_names = [
                    'ignore',
                    'do not post',
                    'empty',
                ];

                // Convert to a plain javascript object, not a VUE object with reactivity :o
                let haystack = JSON.parse(JSON.stringify(this.selected.sale.sale.albums));
                let needles = JSON.parse(JSON.stringify(this.selected.listables));

                haystack = _.chain(haystack)
                    .map(function(album){

                        album.name = album.name.toLowerCase() // lowercase string
                                .replace(' - ',' ').trim() // replace dash separators with single space
                                .replace(/\s+/g,' ') // replace extra spaces with single space
                                .replace(/ \$[0-9]+$/, '') // Remove all $dollars with empty
                                .replace(/all sizes/g, '') // FIXME: interesting results.
                            + ''; // Add white space to the end of the string... possible bugfix

                        // Add style key/value as a 2nd weighted search option
                        album.style = album.name.trim();

                        return album;
                    }).filter(function(album){
                        // filter out albums that match our ignored_names haystack
                        return ignored_names.indexOf(album.name) == -1 ? album : false;
                    }).value();

                let inventoryMatcher = new InventoryToAlbumMatcher(haystack, needles);
                inventoryMatcher.performSearch().then(()=>{
                    this.matching_listables.matches = inventoryMatcher.matches();
                    this.matching_listables.misses = inventoryMatcher.misses();

                    let assigned = this.assignMatchingsToAlbums(inventoryMatcher.matchResults(), inventoryMatcher.misses());

                    if (assigned === false) {
                        return false;
                    }

                    return true;
                });
            },

            assignMatchingsToAlbums(matching_albums, misses){
                var that = this;
                if (that.matching_listables.misses.length == that.selected.listables.length) {
                    // shit, not a single match!?
                    return false;
                }

                for (let i =0; i < matching_albums.length; i++){
                    let matching_results = matching_albums[i];
                    let listables = [];

                    for (let k = 0; k < matching_results.results.length; k++){
                        listables.push(matching_results.results[k].listable);
                    }
                    let groupIndex = _.findIndex(this.postables.facebookgroups, {id: this.selected.sale.sale_id});

                    if (groupIndex > -1) {
                        let group = this.postables.facebookgroups[groupIndex];
                        var albumIndex = _.findIndex(group.albums, {id: matching_results.key});
                        if (albumIndex > -1) {
                            var album = group.albums[albumIndex];
                            listables.forEach(function (listable) {
                                that.addItemToAlbum(listable, album);
                            });
                        }
                    }
                }

                // Push our misses, to the selected listables array.
                that.selected.listables = [];

                for (let i = 0; i < misses.length; i++) {
                    let listable = misses[i];
                    const index = _.findIndex(this.selected.listables, {id: listable.id});
                    if (index == -1) {
                        that.addItemBackToSelected(listable);
                    }
                }

                return true;
            },
            selectFlashsale(sale, e){
                const index = _.findIndex(this.selected.sales, {sale_id: sale.id});

                // So the album is already in our sales and we're unchecking it.
                if (index > -1) {
                    this.selected.sales.splice(index, 1);
                    return;
                }

                if(!this.selected.listables.length) {
                    if (e) {
                        e.preventDefault();
                    }
                    return;
                }

                let selected_sale = JSON.parse(JSON.stringify(this.selected.sale));
                let selected_listables = JSON.parse(JSON.stringify(this.selected.listables));
                selected_sale.album = {};
                selected_sale.album_id = null;
                selected_sale.sale_id = sale.id;
                selected_sale.sale = sale;
                selected_sale.sale_name = sale.name;
                selected_sale.name = sale.name;
                selected_sale.listables = selected_listables;

                this.selected.sales.push(selected_sale);

                this.selected.listables = [];
            },
            selectAlbum(album, e){
                if (album.listables.length) {
                    var that = this;
                    album.listables.forEach(function (listable) {
                        that.addItemBackToSelected(listable);
                    });
                    album.listables = [];
                    return;
                } else {
                    if(!this.selected.listables.length) {
                        if (e) {
                            e.preventDefault();
                        }
                        return;
                    }

                    this.selected.listables.forEach(function (listable) {
                        album.listables.push(listable);
                    });

                    this.selected.listables = [];
                }
            },

            selectFacebookGroup(e){
                let target = e.target;
                // determine the selected album id
                let groupId = target.options[target.selectedIndex].value;
                if (groupId && groupId > -1) {

                    // Need to check if the selected group id has already been added to the saved sales.
                    // If it has, retrieve it.
                    const index = _.findIndex(this.selected.sales, {sale_id: groupId});
                    if (index > -1) {
                        this.selected.sale = this.selected.sales[index];
                        this.selected.sale.magical_matcher = this.selected.sales[index].magical_matcher;
                    } else {
                        this.selected.sale.magical_matcher = false;
                        this.selected.sale.sale_id = groupId;
                        this.selected.sale.sale = this.postables.facebookgroups[_.findIndex(this.postables.facebookgroups, {id: groupId})];
                    }
                }

                $Bus.$emit('listings:create:sale:selected');
            },
            getPostables(){
                var that = this;
                this.actions.refreshing_postables = true;
                this.$http.get(window.location.protocol+'//'+window.location.hostname+'/inventory/postables').then((response)=>{
                    // Called using computed property;
                    if (response.body.data.facebookgroups.length) {
                        response.body.data.facebookgroups.forEach(function (group) {
                            if (group.hasOwnProperty('albums') && group.albums.length) {
                                group.albums.forEach(function (album) {
                                    if (that.postables.facebookgroups.length) {
                                        var groupIndex = _.findIndex(that.postables.facebookgroups, {id: group.id});
                                        var albumIndex = _.findIndex(that.postables.facebookgroups[groupIndex].albums, {id: album.id});
                                        if (groupIndex > -1 && albumIndex > -1) {
                                            album.listables = that.postables.facebookgroups[groupIndex].albums[albumIndex].listables;
                                        } else {
                                            album.listables = [];
                                        }
                                    } else {
                                        album.listables = [];
                                    }
                                });
                            }
                        });
                    }
                    this.postables = response.body.data;
                    this.actions.refreshing_postables = false;
                }, (response)=>{
                    this.actions.refreshing_postables = false;
                });
            },
            selectSale(type){
                let new_sale = selected_sale_data();
                new_sale.sale_type = type;
                this.selected.sale = new_sale;
            },
            gotoStepOne(){
                this.selected.step = 1;
            },
            gotoStepTwo(){
                // If we are currently on step 1, and trying to goto step 2, we need to make sure we've selected
                // some listables in order to goto step 2. without listables, no bueno.
                if (this.selected.step == 1 && (! this.selected.listables || this.selected.listables.length == 0)) {
                    return false;
                }
                this.completed.steps.push(1);
                this.selected.step = 2;
                this.getPostables();
            },
            gotoStepThree(){
                if (this.selected.sales.length == 0) {
                    return false;
                }
                this.completed.steps.push(2);
                this.selected.step = 3
                this.prepareSalesForPreview();
            },
            resetFacebookAlbumSales(){
                var that = this;
                that.postables.facebookgroups.forEach(function (group) {
                    if (group.hasOwnProperty('albums') && group.albums.length) {
                        group.albums.forEach(function (album) {
                            var existingIndex = _.findIndex(that.selected.sales, {album_id: album.id});
                            if (existingIndex > -1) {
                                if (album.listables.length) {
                                    that.selected.sales[existingIndex].listables = album.listables;
                                } else {
                                    that.selected.sales.splice(existingIndex, 1);
                                }
                            } else if (album.listables.length) {
                                that.selected.sales.push({
                                    album: album,
                                    album_id: album.id,
                                    listables: album.listables,
                                    magical_matcher: that.selected.sale.magical_matcher,
                                    sale: that.selected.sale.sale,
                                    sale_id: that.selected.sale.sale.id,
                                    sale_type: 'facebook'
                                });
                            }
                        });
                    }
                });
            },
            saveListing(){
                let flashsales = this.prepared_sales_for_preview.flashsales;
                let facebooksales = this.prepared_sales_for_preview.facebook;
                let facebooksales_total_listables = 0;

                if (flashsales.length == 0 && facebooksales == 0) {
                    return;
                }

                $Bus.$emit('listings:saving');
                this.actions.saving = true;

                if (facebooksales.length) {
                    for (let i = 0; i < facebooksales.length; i++){
                        facebooksales_total_listables += facebooksales[i].listables;
                    }
                }

                let data = {
                    flashsales: flashsales,
                    facebooksales: facebooksales,
                    facebooksales_meta: {
                        total_listables: facebooksales_total_listables
                    },
                    options: this.facebook_listing_options
                };

//                data = btoa(JSON.stringify(data));

                this.$http.post(this.save_endpoint, data).then((response)=>{
                    if (response.body.data.msg) {
                        notify({
                            type: 'success',
                            text: response.body.data.msg
                        })
                    }

                    Object.assign(this.$data, initial_data());
                    this.gotoStepOne();
                }, (error)=>{
                    notify({
                        text: error.body.data.msg
                    })
                }).finally(()=>{
                    this.actions.saving = false;
                    $Bus.$emit('listings:saved');
                });
            }
        },
        computed: {
            display_matches_text(){
                let matches = this.matching_listables.matches;
                let misses = this.matching_listables.misses;

                if (misses.length == 0) {
                    return 'All items matched successfully!';
                }

                if (matches.length == 0) {
                    return 'Unfortunately, no items could be matched.';
                }

                return misses.length +' of '+ (misses.length + matches.length) +' were not matched.';
            },
            notMatchedListablesText(){
                if (this.selected.listables.length) {
                    return "Select an album below to add all "+ this.selected.listables.length +" items into or drag and drop items into specific albums";
                }
                return "No items to place in albums";
            }
        },
        components:{
            'facebook-login' : FacebookLogin,
            'steps' : Steps,
            'spinny' : Spinny,
            'draggable' : draggable,
            'listable-groups' : ListablesGroups,
            'listable' : Listable,
            'listing-settings': ListingSettings
        }
    }
</script>
