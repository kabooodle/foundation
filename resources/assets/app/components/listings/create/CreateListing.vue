<template>
    <div>

        <div class="box white">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-group">
                            <div class="card no-border">
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 1 ?  'text-muted' : null"
                                            class="m-b-0 text-center card-title">
                                        Select Inventory
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.listables.length }} selected</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 2 ?  'text-muted' : null"
                                            class="m-b-0 text-center card-title">Select Sale
                                        <span class="block text-xs text-muted m-t-sm">{{ selected.sales.length }} sales stored</span>
                                    </h5>
                                </div>
                            </div>
                            <div class="card no-border">
                                <div class="card-block">
                                    <h5
                                            :class="selected.step !== 3 ?  'text-muted' : null"
                                            class="m-b-0 text-center card-title">Preview
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box white">
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
                                                    <div style="display: none">
                                                        <span class="avatar_container inline _26 avatar-thumbnail" v-for="listable in selected.sales[_.findIndex(selected.sales, {album_id: album.id})].listables">
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
            <div class="box-footer dker clearfix">
                <template v-if="selected.step == 1">
                    <button
                            @click.prevent="gotoStepTwo"
                            class="btn white btn-sm pull-right">Continue <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></i></button>
                </template>

                <template  v-if="selected.step == 2">
                    <button
                            @click.prevent="gotoStepOne"
                            class="btn white btn-sm pull-left"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back to inventory</button>
                    <button
                            :class="selected.sales.length == 0 ? 'disabled' : null"
                            :disabled="selected.sales.length == 0"
                            @click.prevent="gotoStepThree"
                            class="btn white btn-sm pull-right">Preview listing <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
                </template>

                <template  v-if="selected.step == 3">
                    <button
                            @click.prevent="gotoStepTwo"
                            class="btn white btn-sm pull-left"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back to sales</button>
                    <button
                            @click.prevent="gotoStepTwo"
                            class="btn primary btn-sm pull-right">Save listing</button>
                </template>
            </div>
        </div>
    </div>
</template>
<script>
    const selected_sale_data = function(){
        return {
            sale_type: null,
            sale_id: null,
            sale: null,
            album_id: null,
            album: {},
            listables: [],
        }
    };
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
            selectAlbum(album, e){
                const index = _.findIndex(this.selected.sales, {album_id: album.id});

                // So the album is already in our sales and we're unchecking it.
                if (index > -1) {
                    console.log(index);
                    this.selected.sales.splice(index, 1);
                    return;
                }

                if(!this.selected.listables.length) {
                    e.preventDefault();
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
                if (! this.selected.listables || this.selected.listables.length == 0) {
                    return false;
                }
                this.completed.steps.push(1);
                this.selected.step = 2;
                this.getPostables();
            },
            gotoStepThree(){
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
            'spinny' : Spinny,
            'listable-groups' : ListablesGroups
        }
    }
</script>
