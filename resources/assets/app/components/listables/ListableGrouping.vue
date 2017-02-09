<template>
    <div class="box white">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-2">
                    <h6 class="m-a-0 m-t-sm">
                        <a
                                href="javascript:;"
                                @click.prevent="clickDrawersAll"
                        >{{ group.name }}
                            <span class="text-xs text-muted inline _400">({{ group.total }})
                            <i v-if="group_has_subgroupings"
                               :class="opened.length ? 'fa-angle-up' : 'fa-angle-down'"
                               class="_300 text-sm fa"></i>
                            </span>
                        </a>
                    </h6>
                </div>
                <div class="col-sm-10">
                    <div class="pull-left btn-group-prpl">
                        <button
                                class="btn btn-subgroup white btn-xs"
                                style="margin-right: 3px;"
                                v-for="subgroup in group.subgroupings"
                                :data-subgroup-id="subgroup.id"
                                key="subgroup.id"
                                @click.prevent="clickSubgrouping(subgroup)"
                                :class="selected.listables.length &&  _.chain(selected.listables).where({subgroup: subgroup.id}).value().length == subgroup.listables.length ? 'active' : null"
                        >
                            <input type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;">
                            <span class="text-md">{{ subgroup.name }}</span>
                            <small class="text-sm text-muted block" style="margin-top: -2px;">({{  subgroup.listables.length  }})</small>
                        </button>

                        <button
                                class="btn white btn-xs"
                                style="margin-left: 6px;"
                                @click="clickSubgroupingAll"
                        >
                            <input type="checkbox" style="position: absolute; clip: rect(0,0,0,0); pointer-events: none;">
                            <span class="text-md">ALL</span>
                            <small class="text-sm text-muted block" style="margin-top: -2px;">({{  group.subgroupings.length  }})</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="group_has_subgroupings">
            <div
                    v-if="opened.indexOf(subgroup.id) > -1"
                    v-for="subgroup in group.subgroupings"
                    class="box-size-drawer"
                    :key="subgroup.id"
            >
                <div class="box-divider"></div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <a
                                    href="javascript:;"
                                    @click.prevent="clickSubgroupingDrawer(group, subgroup.id)"
                                    class=" _500 drawer-toggle"
                            >
                                {{ subgroup.name }} <span class="text-muted text-sm"><i class="fa fa-angle-up"></i></span>
                            </a>
                        </div>
                        <div class="col-sm-10">
                            <div class="item-box"  >
                                <div class="row row-horizon">

                                    <div
                                            v-for="item in subgroup.listables"
                                            :key="item.id"
                                            class="col-sm-2 p-r-0 btn-group-prpl" style="width:110px !important;">
                                        <button
                                                @click.prevent="clickListable(subgroup, item.id)"
                                                style="border-radius: .25rem;"
                                                type="button"
                                                class="btn white btn-xs"
                                                :aria-pressed="_.findIndex(selected.listables, {id: item.id}) > -1"
                                                :class="_.findIndex(selected.listables, {id: item.id}) > -1 ? 'active' : null"
                                        >
                                                            <span class="item block avatar-thumbnail _80">
                                                                <img
                                                                        :src="item.cover_photo.location"
                                                                >
                                                            </span>
                                            <span class="p-a-o text-sm clearfix block">
                                                        <span class="pull-left">Qty: <span class="text-muted">{{ item.initial_qty }}</span></span>
                                                            <span class="text-muted pull-right">${{ item.price_usd }}</span>
                                                            </span>
                                        </button>
                                        <div class="clearfix" style="margin-top: 5px;">
                                            <button
                                                    type="button"
                                                    class="btn btn-xs _400 pull-left white"
                                            >Edit</button>
                                            <a
                                                    target="_blank"
                                                    class="btn btn-xs _400 pull-right white"
                                            >Claim</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import ListableButton from './ListableButton.vue';
    export default{
        props: {
            group: {
                required: true,
                type: Object,
            },
            group_type: {
                required: true,
                type: String
            },
        },
        data(){
            return{
                selected: {
                    listables: [],
                    inventory_groupings: [],
                },
                opened: []
            }
        },
        computed: {
            group_has_subgroupings(){
                return this.group.subgroupings.length > 0 && this.group.subgroupings !== undefined;
            },
        },
        created(){
            // API for allowing the deselection of a listing
            // FIXME: We need a better name because this is a global event and we cant have all listeners triggered
            $Bus.$on('listings:listing:unselect '+ this.group.id, (groupid, listingid)=>{
                if (groupid === this.group.id) {
                    // TODO: finish this.
                    // Call unselectListing method.
                }
            });
        },
        methods: {
            addSubgroupingDrawer(id){
                // BUGFIX:
                // Sometimes, we trigger events to open a drawer.
                // Said events are unaware of the drawers state, so we need to prevent
                // duplicate entries.
                const index = this.opened.indexOf(id);
                if (index == -1) {
                    this.opened.push(id);
                }
            },
            removeSubgroupingDrawer(id){
                const index = this.opened.indexOf(id);
                if (index > -1) {
                    this.opened.splice(index, 1);
                }
            },
            addSubgrouping(subgroup, subgroup_id){
                this.selected[this.group_type].push(subgroup_id);

                _.each(subgroup.listables, (item)=>{
                    this.addListable(subgroup, item.id);
                });

                if (this.group_has_subgroupings) {
                    this.addSubgroupingDrawer(subgroup_id);
                }
            },
            removeSubgrouping(index, subgroup, subgroup_id){
                this.selected[this.group_type].splice(index, 1);

                // Iterate over all the listables and remove them
                _.each(subgroup.listables, (item)=>{
                    this.removeListable(subgroup, item.id);
                });

                if (this.group_has_subgroupings) {
                    this.removeSubgroupingDrawer(subgroup_id);
                }
            },

            addListable(subgroup, id){
                const value = {id: id, subgroup: subgroup.id};
                const index = _.findIndex(this.selected.listables, value);
                if (index == -1) {
                    this.selected.listables.push(value);
                }
            },
            removeListable(group, id){
                const index = _.findIndex(this.selected.listables, {id: id});
                if (index > -1) {
                    this.selected.listables.splice(index, 1);
                }
            },

            clickSubgroupingDrawer(group, subgroupid){
                const index = this.opened.indexOf(subgroupid);
                if (index > -1){
                    // Drawer is opened, close it;
                    this.removeSubgroupingDrawer(subgroupid);
                } else {
                    // Drawer is closed, open it
                    this.addSubgroupingDrawer(subgroupid);
                }
            },


            clickDrawersAll(){
                let group = this.group;
                // If we dont have subgroupings, then we dont have drawers.
                if (!this.group_has_subgroupings) {
                    return;
                }

                if (this.opened.length) {
                    this.opened = [];
                } else {
                    // open everything
                    _.each(group.subgroupings, (subgroup)=>{
                        this.addSubgroupingDrawer(subgroup.id);
                    });
                }
            },

            clickListable(subgroup, listableid){
                const index = _.findIndex(this.selected.listables, {id: listableid})
                if (index > -1) {
                    // Found, so we need to unselect the listable
                    this.removeListable(subgroup, listableid);
                } else {
                    // Not found, select the listable
                    this.addListable(subgroup, listableid);
                }
            },

            /**
             * Click handler when a subgrouping is clicked
             * @param group
             * @param id
             * @param forceSelect
             */
            clickSubgrouping(subgroup){
                const index = _.findIndex(this.selected.listables, {subgroup: subgroup.id});
                if (index > -1) {
                    // Remove all listables for this subgrouping
                    _.each(subgroup.listables, (item)=>{
                        this.removeListable(subgroup, item.id);
                    });

                    // close the drawer for the subgroup
                    if (this.group_has_subgroupings) {
                        this.removeSubgroupingDrawer(subgroup.id);
                    }
                } else {
                    // add all listables for this subgrouping
                    _.each(subgroup.listables, (item)=>{
                        this.addListable(subgroup, item.id);
                    });

                    // Open the drawer for the subgroup
                    if (this.group_has_subgroupings) {
                        this.addSubgroupingDrawer(subgroup.id);
                    }
                }
            },

            /**
             * Click handler when "ALL" is selected for a subgroup
             */
            clickSubgroupingAll(){
                // If we already have items selected,\then we are unselecting everything.
                // Otherwise, we are selecting everything.
                // Yup - the code makes a lame assumption :)
                let group = this.group;
                // FIXME: This needs to select ALL listings, not subgroupings.
                if (this.selected[this.group_type].length) {
                    this.selected[this.group_type] = [];
                } else {
                    _.each(group.subgroupings, (subgroup)=>{
                        let index = this.selected[this.group_type].indexOf(subgroup.id);
                        if (index == -1) {
                            this.addSubgrouping(group, subgroup.id);
                        }
                    });
                }
            },
        },
        components:{
            'spinny' : Spinny,
            'listable-button' : ListableButton
        }
    }
</script>
