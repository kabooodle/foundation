<template>
    <span>
        <inline-field>
            <template slot="label">Group</template>
            <template slot="input">
                <multiselect
                        label="name"
                        track-by="id"
                        placeholder="Select a group"
                        v-model="group"
                        :options="seller_groups"
                        :multiple="false"
                        :searchable="true"
                        :loading="loading.groups"
                        :internal-search="false"
                        :clear-on-select="true"
                        :close-on-select="true"
                        :options-limit="10"
                        :limit="10"
                        @select="groupSelected"
                        @search-change="searchGroups">
                    <template slot="option" scope="props">
                        <div class="option__desc">
                            <span class="option__title">{{ props.option.name }} ({{ props.option.users.length }} members)</span>
                        </div>
                    </template>
                </multiselect>
            </template>
        </inline-field>

        <inline-field class="m-b-0">
            <template slot="label">Time slot</template>
            <input slot="input" type="text" class="form-control" v-model="time_slot" name="">
            <span class="text-xs text-muted" slot="text-help">(optional)</span>
        </inline-field>
    </span>
</template>
<script>
    import InlineField from '../InlineField.vue';
    import Multiselect from 'vue-multiselect';
    export default{
        props: {
            id: {
                required: true
            },
            search_endpoint: {
                required: true,
                type: String
            },
            selected_groups: {
                type: Array
            },
        },
        data(){
            return{
                previousRequest :null,
                loading: {
                    groups: false
                },
                seller_groups: [],
                group: [],
                time_slot: null,
            }
        },
        created(){
            $Bus.$on('flashsale:saving',()=>{
                $Bus.$emit('flashsale:component:data', {
                    id: this.group.id,
                    time_slot: this.time_slot
                });
            });
        },
        methods:{
            groupSelected(option,id){
                $Bus.$emit('sellergroup:selected', option, this.id);
            },
            searchGroups(query){
                if (query.trim() == '') {
                    return;
                }
                this.loading.groups = true;
                this.$http.post(this.search_endpoint, {q: query, exclude: _.pluck(this.selected_groups, 'id')},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.seller_groups = response.body.data.data;
                    this.loading.groups = false;
                });
            },
        },
        components:{
            'multiselect' : Multiselect,
            'inline-field' : InlineField,
        }
    }
</script>