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
                                    <div class="form-group m-t-1 m-b-1" v-if="selected.sale.sale_id">
                                        <hr>
                                        <div class="checkbox checkbox-slider--b-flat">
                                            <label>
                                                <input
                                                        :checked="selected.sale.magical_matcher"
                                                        @change="matchInventory"
                                                        data-type="magic-toggler"
                                                        type="checkbox" />
                                                <span class="text-sm">Automatically match inventory?</span>
                                            </label>
                                        </div>
                                        <hr>
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
                                                    <div class="m-l-2">
                                                        <span class="avatar_container m-r-sm inline _26 avatar-thumbnail" v-for="listable in selected.sales[_.findIndex(selected.sales, {album_id: album.id})].listables">
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
    const search_match_result = function(){
        return {
            album_id: null,
            album: null,
            listable_id: null,
            listable: {},
            match: null,
            match_score: null,
            needle: null,
        }
    };
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
                }
            },

            buildInventoryAlbumMatchings(){
                // TODO: Are there pre-requisites we need to check before we run this?

                if (this.selected.listables.length == 0) {
                    notify({
                        text: 'You must first select inventory.'
                    });

                    return false;
                }

                // Array holding results of the search.
                let results = [];
                const MIN_SCORE = 90;

                // possible names we can search against and ignore
                let ignored_names = [
                    'ignore',
                    'do not post',
                    'empty',
                ];

                // Convert to a plain javascript object, not a VUE object with reactivity :o
                let haystack = JSON.parse(JSON.stringify(this.selected.sale.sale.albums));

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

                var options = {
                    include: ["score"],
                    shouldSort: true,
                    threshold: 0.3,
                    location: 0,
                    distance: 0,
                    maxPatternLength: 100,
                    minMatchCharLength: 3,
                    keys: [{
                        name: "name",
                        weight: .6,
                    }, {
                        name: "style",
                        weight: .4
                    }]
                };

                // Fuse variable containing the fuse object, with the haystack queued and the options we set.
                var fuse = new Fuse(haystack, options);

                // Iterate over each of the selected inventory items, and search for albums that match
                // the inventory item's "style" "size" (e.g. Adeline XXL, Adeline 5)
                _.each(this.selected.listables, (listable)=>{

                    // Convert VUE object to plain object so we dont have stupid nested listeners
                    let temp_listable = JSON.parse(JSON.stringify(listable));

                    // Build the needle we are looking for in the haystack, or rather,
                    // string we are searching for in the array of possibilities.
                    let style_name = listable.style_name.toLowerCase();
                    let size_name = listable.size_name.toLowerCase();
                    let needle = style_name+' '+size_name;

                    // Search through the haystack, comparing our needle, and return array of all possible matches
                    let found_results = fuse.search(needle);

                    // Glass is half empty; Start with having no match;
                    let match = false;

                    // Our ideal match will always be the first key in the array of possible matches
                    // as the keys are sorted by the highest score.  We dont care about anything but this key.
                    let ideal_match = found_results[0];

                    // If we have an ideal match, we further qualify the match based on the score.
                    // If its >= our MIN_SCORE, we will consider this match as our matching album.
                    if (ideal_match && (1 - ideal_match.score) * 100 >= MIN_SCORE) {
                        match = ideal_match;
                    }
                    // No ideal match, so lets search now using a different needle, just to be sure.
                    else {
                        found_results = fuse.search(style_name);
                        let style_based_ideal_match = found_results[0];

                        if (style_based_ideal_match && ((1 - style_based_ideal_match.score) * 100 >= MIN_SCORE)) {
                            match = style_based_ideal_match;
                        }
                    }

                    // Result object
                    let result = search_match_result();
                    result.album_id = match ? match.item.id : null;
                    result.album = match ? match.item : null;
                    result.listable_id = temp_listable.id;
                    result.listable = temp_listable;
                    result.match = match ? match : null;
                    result.match_score = match ? ((1 - match.score) * 100) : null;
                    result.needle = needle;

                    if (match) {
                        const index = _.findIndex(results, {key: result.album_id});
                        if (index == -1) {
                            results.push({key: result.album_id, results: [result]});
                        } else {
                            results[index].results.push(result);
                        }

                        this.matching_listables.matches.push(temp_listable);
                    } else {
                        this.matching_listables.misses.push(temp_listable);
                    }
                });


                let assigned = this.assignMatchingsToAlbums(results);

                if (assigned === false) {
                    return false;
                }

                return true;
            },

            assignMatchingsToAlbums(matching_albums){
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
                this.selected.listables = this.matching_listables.misses;

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
                        this.selected.sale.sale_id = groupId;
                        this.selected.sale.sale = this.postables.facebookgroups[_.findIndex(this.postables.facebookgroups, {id: groupId})];
                    }
                }

//                if (_.findIndex(this.selected.sales, {sale_id: this.selected.sale.sale_id}) == -1){
//                    this.pushActiveSaleToSelectedSales();
//                }
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

                // In the event we are going from step two, back to step one,
                // save the active sale configuration, by pushing it to the selected.sales array.
                this.pushActiveSaleToSelectedSales();
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

                // Save the active sale.
                this.pushActiveSaleToSelectedSales();
            },

            pushActiveSaleToSelectedSales(){
                // If we have an active sale, and the active sale isn't empty, continue;
//                if (this.selected.sale && ! _.isEmpty(this.selected.sale)) {
//                    // Does the active sale have an id? This id is the id of a facebook group or flash sale.
//                    // If so, save the sale by pushing it to the array.
//                    const index = _.findIndex(this.selected.sales, {sale_id: this.selected.sale_id});
//                    if (this.selected.sale.sale_id && index == -1) {
////                        this.selected.sales.push(this.selected.sale);
//                    }
//                }
//
//                // Reset selected sale to empty.
                this.selected.sale = {};
            },
        },
        components:{
            'steps' : Steps,
            'spinny' : Spinny,
            'listable-groups' : ListablesGroups
        }
    }
</script>
