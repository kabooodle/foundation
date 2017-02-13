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
                                :class="selected.listables.length &&  _.chain(selected.listables).where({subgroup: subgroup}).value().length == subgroup.listables.length ? 'active' : null"
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
                                    @click.prevent="clickSubgroupingDrawer(group, subgroup)"
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
                                                @click.prevent="clickListable(subgroup, item)"
                                                style="border-radius: .25rem;"
                                                type="button"
                                                class="btn white btn-xs"
                                                :aria-pressed="_.findIndex(selected.listables, {id: item.id}) > -1"
                                                :class="_.findIndex(selected.listables, {id: item.id}) > -1 ? 'active' : null"
                                        >
                                            <span class="item block avatar-thumbnail _80">
                                                <img :src="item.cover_photo.location" >
                                            </span>
                                            <span class="p-a-o text-sm clearfix block">
                                                <span class="pull-left">Qty:
                                                    <span class="text-muted">{{ item.initial_qty }}</span>
                                                </span>
                                                <span class="text-muted pull-right">${{ item.price_usd }}</span>
                                            </span>
                                        </button>
                                        <div v-if="display_footer_buttons" class="clearfix" style="margin-top: 5px;">
                                            <button
                                                    @click.prevent="editItemButtonClicked(item, $event)"
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
    const initial_state = function(){
        return {
            selected: {
                listables: [],
                inventory_groupings: [],
            },
            opened: []
        };
    };
    import Spinny from '../Spinner.vue';
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
            display_footer_buttons: {
                type: Boolean,
                default: false,
            }
        },
        data(){
            return initial_state();
        },
        computed: {
            group_has_subgroupings(){
                return this.group.subgroupings.length > 0 && this.group.subgroupings !== undefined;
            },
        },
        created(){
            // curious about how we can handle slots as a javascript variable
//            console.log(this.$slots);

            $Bus.$on('listings:listables:select:all', ()=>{
                _.each(this.group.subgroupings, (subgrouping)=>{
                    _.each(subgrouping.listables, (listable)=>{
                        this.addListable(subgrouping, listable);
                    });
                });
            });

            $Bus.$on('listings:selected:listables:reset', ()=>{
                Object.assign(this.$data, initial_state());
                // For some reason, the below loop does not work.
                // Only a few are deselected, others fail. It's bizarre.
                // So for now, reset state.

//                if (this.selected.listables.length > 0) {
//                    _.each (this.selected.listables, (listable)=>{
//                        if (listable && listable.hasOwnProperty('subgroup')){
//                            this.removeListable({id: listable.subgroup}, listable);
//                        }
//                    });
//                }
            });

            // GET selected listables event listener
            $Bus.$on(this.group.id+'::listings:selected:listables:get', ()=>{
                $Bus.$emit(this.group.id+'::listings:selected:listables', this.group.id, subgroup_id, this.selected.listables);
            });
        },
        methods: {
            editItemButtonClicked(item, event){
                $Bus.$emit('popout-overlay:request-open');

                this.$http.get(window.location.href+'/'+item.name_uuid+'/edit', {
                    async: false,
                    before(request) {
                        // Before each ajax request, abort the previous request
                        // and add this request to an array of requests for reference.
                        $Bus.$emit('popout-overlay:change-prompt', false);
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response)=>{
                    setTimeout(()=>{
                        $Bus.$emit('popout-overlay:change-content', response.body);
                    },0);
                }, (response)=>{
                    $Bus.$emit('popout-overlay:change-content', 'An error occurred, please try again.', false);
                });
            },
            resetState(){
                Object.assign(this.$data, initial_state());
            },
            addSubgroupingDrawer(subgroup){
                // BUGFIX:
                // Sometimes, we trigger events to open a drawer.  Said events are unaware of the drawers state,
                // so we need to prevent duplicate entries.
                const index = this.opened.indexOf(subgroup.id);
                if (index == -1) {
                    this.opened.push(subgroup.id);
                }
            },

            removeSubgroupingDrawer(subgroup){
                const index = this.opened.indexOf(subgroup.id);
                if (index > -1) {
                    this.opened.splice(index, 1);
                }
            },

            addListable(subgroup, listable){
                const value = {id: listable.id, subgroup: subgroup};
                const index = _.findIndex(this.selected.listables, value);
                if (index == -1) {
                    this.selected.listables.push(value);
                    $Bus.$emit('listings:selected:listable:added', this.group, subgroup, listable);
                }
            },

            removeListable(subgroup, listable){
                const index = _.findIndex(this.selected.listables, {id: listable.id});
                if (index > -1) {
                    this.selected.listables.splice(index, 1);
                    $Bus.$emit('listings:selected:listable:removed', this.group, subgroup, listable);
                }
            },

            clickSubgroupingDrawer(group, subgroup){
                const index = this.opened.indexOf(subgroup.id);
                if (index > -1){
                    // Drawer is opened, close it;
                    this.removeSubgroupingDrawer(subgroup);
                } else {
                    // Drawer is closed, open it
                    this.addSubgroupingDrawer(subgroup);
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
                        this.addSubgroupingDrawer(subgroup);
                    });
                }
            },

            clickListable(subgroup, listable){
                const index = _.findIndex(this.selected.listables, {id: listable.id})
                if (index > -1) {
                    // Found, so we need to unselect the listable
                    this.removeListable(subgroup, listable);
                } else {
                    // Not found, select the listable
                    this.addListable(subgroup, listable);
                }
            },

            /**
             * Click handler when a subgrouping is clicked
             * this will add ALL listables or remove ALL listables that are associated to this subgroup
             *
             * @param group
             * @param id
             * @param forceSelect
             */
            clickSubgrouping(subgroup, force){
                const index = _.findIndex(this.selected.listables, {subgroup: subgroup});
                if (index > -1) {
                    // Remove all listables for this subgrouping
                    _.each(subgroup.listables, (item)=>{
                        this.removeListable(subgroup, item);
                    });

                    // close the drawer for the subgroup
                    if (this.group_has_subgroupings) {
                        this.removeSubgroupingDrawer(subgroup);
                    }
                } else {
                    // add all listables for this subgrouping
                    _.each(subgroup.listables, (item)=>{
                        this.addListable(subgroup, item);
                    });

                    // Open the drawer for the subgroup
                    if (this.group_has_subgroupings) {
                        this.addSubgroupingDrawer(subgroup);
                    }
                }
            },

            /**
             * Click handler when "ALL" is selected for a subgroup
             */
            clickSubgroupingAll(){
                const adding = this.selected.listables.length == 0;
                _.each(this.group.subgroupings, (subgroup)=>{
                    _.each(subgroup.listables, (listable)=>{
                        if (adding) {
                            this.addListable(subgroup, listable);
                        } else {
                            this.removeListable(subgroup, listable);
                        }
                    });
                });
            }
        },
        components:{
            'spinny' : Spinny,
        }
    }
</script>
