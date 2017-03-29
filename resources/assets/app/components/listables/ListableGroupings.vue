<template>
    <div>
            <div v-if="show_select_buttons" class="text-center m-b-1 center-block">
                <button
                        style="width: 140px"
                        :disabled="actions.fetching_listables ? true : false"
                        :class="actions.fetching_listables ? 'disabled' :  null"
                        @click.prevent="selectAllListables" class="btn white btn-md">
                    Select all
                    <span class="text-sm text-muted block">({{this.listables.length}})</span>
                </button>
                <button
                        style="width: 140px"
                        :disabled="actions.fetching_listables ? true : false"
                        :class="actions.fetching_listables ? 'disabled' :  null"
                        @click.prevent="resetSelectedListables" class="btn white btn-md">
                    Unselect all
                    <span class="text-sm text-muted block">({{this.listables.length}})</span>
                </button>

                        <div class="text-center center-block center m-t-sm">
                                <label class="checkbox">
                                    <input
                                            @change="toggleDisableUnavailable"
                                            type="checkbox"
                                            value="1"
                                            class="has-value"
                                           :checked="disableUnavailable"
                                    ><span class="text-sm">Disable unavailable items</span>
                                </label>
                                <label class="checkbox">
                                    <input
                                            @change="toggleHideUnavailable"
                                            :checked="hideUnavailable"
                                            type="checkbox"
                                            value="1"
                                            class="has-value">
                                    <span class="text-sm">Hide unavailable items</span>
                                </label>
                        </div>
            </div>
            <listable-grouping
                v-for="group in listable_groupings"
                key="group.id"
                group_type="inventory"
                :group="group"
                :display_footer_buttons="display_footer_buttons"
                :display_toggle_all_buttons="display_toggle_all_buttons"
                :disable-unavailable="disableUnavailable"
                :hide-unavailable="hideUnavailable"
                :inventory-index-route=inventoryIndexRoute
            ></listable-grouping>
        <div v-if="actions.fetching_listables" class="text-center center-block">
            <spinny :size="'20'"></spinny>
        </div>
        <popout-overlay></popout-overlay>
    </div>
</template>
<script>
    import PopoutOverlay from '../Popover.vue';
    import Spinny from '../Spinner.vue';
    import ListableGrouping from './ListableGrouping.vue';
    export default{
        props: {
            show_select_buttons: {
                default: false,
            },
            listablegroupings_endpoint: {
                required: true,
                type: String
            },
            display_toggle_all_buttons: {
                type: Boolean,
                default: true
            },
            display_footer_buttons: {
                type: Boolean,
                default: false
            },
            inventoryIndexRoute: {
                type: String,
                required: self.display_footer_buttons,
            },
            initialDisableUnavailable: {
                type: Boolean,
                //required: true,
                default: false
            },
        },
        data(){
            return{
                disableUnavailable: this.initialDisableUnavailable,
                hideUnavailable: false,
                listables: [],
                listable_groupings: [],
                actions: {
                    fetching_listables: false,
                },
                selected: {
                    listables: [],
                }
            }
        },
        mounted(){
            this.getListables();

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
            toggleHideUnavailable(){
                this.hideUnavailable = (!this.hideUnavailable);
            },
            toggleDisableUnavailable(){
                this.disableUnavailable = (!this.disableUnavailable);
            },
            resetSelectedListables(){
                $Bus.$emit('listings:selected:listables:reset');
            },
            selectAllListables(){
                $Bus.$emit('listings:listables:select:all');
            },
            getListables(endpoint){
                this.actions.fetching_listables = true;
                this.$http.get(endpoint ? endpoint : this.listablegroupings_endpoint).then((response)=>{
                    let pagination = response.body.data.meta;
                    if (response.body.data.data.length > 0) {
                        _.each(response.body.data.data, (groupings)=>{
                            this.listable_groupings.push(groupings);
                            _.each(groupings.subgroupings, (group)=>{
                                _.each(group.listables, (listable)=>{
                                    this.listables.push(listable);
                                })
                            })
                        });
                    }

                    this.listable_groupings = _.sortBy(this.listable_groupings, ['name']);
                    this.actions.fetching_listables = false;
                    if (pagination.next_page_url) {
                        this.getListables(pagination.next_page_url);
                    } else {
                        $Bus.$emit('listables:fetched', this.listable_groupings);
                    }
                });
            }
        },
        components:{
            'spinny' : Spinny,
            'listable-grouping' : ListableGrouping,
            'popout-overlay' : PopoutOverlay,
        }
    }
</script>
