<template>
    <div>
        <div class="text-center center-block" v-if="actions.fetching_listables">
            <spinny :size="'20'"></spinny>
        </div>
        <listable-grouping
                v-else
                v-for="group in listable_groupings"
                key="group.id"
                :group="group"
                group_type="inventory"
                :display_footer_buttons="true"
        ></listable-grouping>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import ListableGrouping from './ListableGrouping.vue';
    export default{
        props: {
            listablegroupings_endpoint: {
                required: true,
                type: String
            },
            display_footer_buttons: {
                type: Boolean,
                default: false
            }
        },
        data(){
            return{
                actions: {
                    fetching_listables: false,
                },
                listable_groupings: []
            }
        },
        mounted(){
            this.getListables();
        },
        methods: {
            getListables(){
                this.actions.fetching_listables = true;
                this.$http.get(this.listablegroupings_endpoint).then((response)=>{
                    this.listable_groupings = response.body.data;
                }).finally(()=>{
                    this.actions.fetching_listables = false;
                    $Bus.$emit('listables:fetched', this.listable_groupings);
                });
            }
        },
        components:{
            'spinny' : Spinny,
            'listable-grouping' : ListableGrouping
        }
    }
</script>
