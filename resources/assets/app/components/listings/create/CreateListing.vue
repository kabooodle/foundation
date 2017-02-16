<template>
    <div>

        <div class="box white">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-group">
                            <div class="card no-border text-center">
                                <img class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/inventory.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 1 ?  'text-muted' : null"
                                            class="m-b-0 card-title">
                                        Select Inventory
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.listables.length }} selected</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <img class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/sell_and_display.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 2 ?  'text-muted' : null"
                                            class="m-b-0 text-center card-title">Select Sale
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.sales.length }} sales prepared</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <img class="card-img-top hidden-sm-down center-block" height="64" src="/assets/images/home/icons/preview.png" >
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 3 ?  'text-muted' : null"
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
                            :display_footer_button="0"
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
                                            :class="postables.facebookgroups.length == 0 ? 'disabled' : null"
                                            :disabled="postables.facebookgroups.length == 0 "
                                            @click="selectSale('facebook')"
                                            class="btn text-center white btn-lg btn-block">
                                        Facebook
                                        <span class="text-sm text-muted block">{{postables.facebookgroups.length}} groups</span>
                                    </button>
                                </div>
                                <div class="card no-border">
                                    <button
                                            :class="postables.flashsales.length == 0 ? 'disabled' : null"
                                            :disabled="postables.flashsales.length == 0 "
                                            @click="selectSale('flashsale')"
                                            class="btn text-center white btn-lg btn-block">
                                        Flash sale
                                        <span class="text-sm text-muted block">{{postables.flashsales.length}} sales</span>
                                    </button>
                                </div>
                            </div>

                            <template v-if="selected.sale">
                                <div class="form-group m-t-1" v-if="selected.sale.sale_type == 'flashsale'">
                                    <label class="control-label">Select sale</label>
                                    <select class="form-control"></select>
                                </div>
                                <template v-if="selected.sale.sale_type == 'facebook'">
                                    <div class="form-group m-t-1">
                                        <label class="control-label">Select group</label>
                                        <select @change="selectFacebookGroup" class="form-control">
                                            <option value="-1"></option>
                                            <option v-for="fbgroup in postables.facebookgroups" :value="fbgroup.id">{{ fbgroup.name }}</option>
                                        </select>
                                    </div>
                                    <div class="box white m-t-1 m-b-1" v-if="selected.sale.sale_id">
                                        <div class="box-body">
                                            <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                                <label>
                                                    <input
                                                            :checked="selected.sale.magical_matcher"
                                                            @change="matchInventory"
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
                                    <div class="form-group m-t-1" v-if="selected.sale.sale_id">
                                        <label class="control-label">Select album to add inventory</label>
                                        <div class="m-b-sm" v-for="album in postables.facebookgroups[_.findIndex(postables.facebookgroups, {id: selected.sale.sale_id})].albums" >
                                            <label class="form-check-label block md-check">
                                                <input
                                                        :data-album-id="album.id"
                                                        :checked="_.findIndex(selected.sales, {album_id: album.id}) > -1"
                                                        key="album.id"
                                                        :value="selected.sale.sale_id"
                                                        :name="album.id"
                                                        @click="selectAlbum(album, $event)"
                                                        class="form-check-input"
                                                        type="checkbox">
                                                <i class="indigo"></i>
                                                <span
                                                        :class="_.findIndex(selected.sales, {album_id: album.id}) > -1 ? 'text-primary' : 'text-muted'"
                                                >{{ album.name }}</span>
                                            </label>
                                            <template v-if="_.findIndex(selected.sales, {album_id: album.id}) > -1">
                                                <div class="m-t-0">
                                                    <span class="text-xs text-muted m-l-2">{{ selected.sales[_.findIndex(selected.sales, {album_id: album.id})].listables.length }} items associated</span>
                                                    <div class="m-l-2" style="display: none">
                                                        <span class="avatar_container m-r-sm inline _32 avatar-thumbnail" v-for="listable in selected.sales[_.findIndex(selected.sales, {album_id: album.id})].listables">
                                                            <img :src="listable.cover_photo.location">
                                                        </span>
                                                    </div>
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
                    I am step 3
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
    import Fuse from 'fuse.js';
    import Steps from './Steps.vue';
    import Spinny from '../../Spinner.vue';
    import ListablesGroups from '../../listables/ListableGroupings.vue';
    import InventoryToAlbumMatcher from './InventoryToAlbumMatcher';
    export default{
        props: {
            show_select_buttons: {
                default: true,
            },
            endpoint: {
                type: String
            },
        },
        data(){
            return {
                // All available listables for selection.
                listables: [],

                // All available sales
                postables: [],

                actions: {
                    refreshing_data : true,
                    refreshing_postables: false,
                    resetting_sales: false,
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
            }
        },
        created() {
            this.gotoStepOne();

            $Bus.$on('listables:fetched', (listables)=>{
                this.listables = listables;
                this.actions.refreshing_data = false;
            });

            $Bus.$on('listings:selected:listables:reset', ()=>{
                this.selected.listables = [];
            });

            $Bus.$on('listings:selected:listable:added', (group, subgroup, listable)=>{
                const index = _.findIndex(this.selected.listables, {id: listable.id});
                if (index == -1) {
                    this.selected.listables.push(listable);
                }
            });

            $Bus.$on('listings:selected:listable:removed', (groupid, subgroup, listable)=>{
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
        methods: {
            matchInventory(e){
                let target = e.target;
                if (target.checked) {
                    // Run the tool magic tool.
                    let matched = this.buildInventoryAlbumMatchings();
                    if (matched === false) {
                        target.checked = false;
                    }
                    this.selected.sale.magical_matcher = true;
                } else {
                    // Tool was already run, they want to undo the results
                    this.selected.sale.magical_matcher = false;
                    this.resetAllSalesForGroup();
                }
            },

            resetAllSalesForGroup(){
                this.actions.resetting_sales = true;

                // loop through the saved sales, and extract the listables.
                let selected_sales_for_group = _.filter(this.selected.sales, {sale_id: this.selected.sale.sale_id});

                for (let i = 0; i < selected_sales_for_group.length; i++) {
                    let sale = selected_sales_for_group[i];
                    for (let k = 0; k < sale.listables.length; k++) {
                        let listable = sale.listables[k];

                        // Give me the listable back, but only if I dont already have it!
                        const index = _.findIndex(this.selected.listables, {id: listable.id});
                        if (index == -1) {
                            this.selected.listables.push(listable);
                        }
                    }
                }

                // RIP, saved sales.
                // (plays taps)
                _.remove(this.selected.sales, {sale_id: this.selected.sale.sale_id});
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
                                .replace(/\s+/g,' ').trim() // replace extra spaces with single space
                                .replace(/ \$[0-9]+$/, '') // Remove all $dollars with empty
                                .replace(/all sizes/g, '') // FIXME: interesting results.
                            + ' '; // Add white space to the end of the string... possible bugfix

                        // Add style key/value as a 2nd weighted search option
                        album.style = album.name.trim();

                        return album;
                    }).filter(function(album){
                        // filter out albums that match our ignored_names haystack
                        return ignored_names.indexOf(album.name) == -1 ? album : false;
                    }).value();

                let inventoryMatcher = new InventoryToAlbumMatcher(haystack, needles);
                inventoryMatcher.performSearch();

                this.matching_listables.matches = inventoryMatcher.matches();
                this.matching_listables.misses = inventoryMatcher.misses();

                let assigned = this.assignMatchingsToAlbums(inventoryMatcher.matchResults(), inventoryMatcher.misses());

                if (assigned === false) {
                    return false;
                }

                return true;
            },

            assignMatchingsToAlbums(matching_albums, misses){
                if (this.matching_listables.misses.length == this.selected.listables.length) {
                    // shit, not a single match!?
                    return false;
                }

                _.each(matching_albums, (matching_results)=>{
                    let listables = [];
                    _.each(matching_results.results, (result)=>{
                        listables.push(result.listable);
                    });

                    let sale_data = JSON.parse(JSON.stringify(this.selected.sale));
                    sale_data.album = matching_results.results[0].album;
                    sale_data.album_id = matching_results.results[0].album.id;
                    sale_data.listables = listables;

                    this.selected.sales.push(sale_data);
                });

                // TODO: Make sure that each listable we are pushing back to the
                // listables array, isn't already there.
                this.selected.listables = misses;

                return true;
            },

            selectAlbum(album, e){
                const index = _.findIndex(this.selected.sales, {album_id: album.id});

                // So the album is already in our sales and we're unchecking it.
                if (index > -1) {
                    this.selected.sales.splice(index, 1);
                    return;
                }

                // TODO: Upon unchecking a checkbox, when magical matcher has been enabled,
                // the inventory associated to this album should be returned to the available
                // listables array.
//                if (this.selected.magical_matcher) {
//                    this.selected.listables.push(album.listables);
//                }

                if(!this.selected.listables.length) {
                    if (e) {
                        e.preventDefault();
                    }
                    return;
                }

                let selected_sale = JSON.parse(JSON.stringify(this.selected.sale));
                let selected_listables = JSON.parse(JSON.stringify(this.selected.listables));
                selected_sale.album = JSON.parse(JSON.stringify(album));
                selected_sale.album_id = album.id;
                selected_sale.listables = selected_listables;

                this.selected.sales.push(selected_sale);

                this.selected.listables = [];
                this.selected.sale.sale = {};
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
                    } else {
                        this.selected.sale.magical_matcher = false;
                        this.selected.sale.sale_id = groupId;
                        this.selected.sale.sale = this.postables.facebookgroups[_.findIndex(this.postables.facebookgroups, {id: groupId})];
                    }
                }

                $Bus.$emit('listings:create:sale:selected');
            },
            getPostables(){
                this.actions.refreshing_postables = true;
                this.$http.get(window.location.protocol+'//'+window.location.hostname+'/inventory/postables').then((response)=>{
                    // Called using computed property;
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
                    return;
                }
                this.completed.steps.push(2);
                this.selected.step = 3
            },
        },
        computed: {
            display_matches_text(){
                let matches = this.matching_listables.matches;
                let misses = this.matching_listables.misses;

                if (misses.length == 0) {
                    return 'All items matched successfully!';
                }

                if (matches.length == 0) {
                    return 'Unfortunately no items could be matched.';
                }

                return misses.length +' of '+ (misses.length + matches.length) +' were not matched.';
            },
        },
        components:{
            'steps' : Steps,
            'spinny' : Spinny,
            'listable-groups' : ListablesGroups
        }
    }
</script>
