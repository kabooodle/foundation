<template>
    <span :id="'group_'+id">
        <inline-field>
            <template slot="label">Group</template>
            <template slot="input">
                <multiselect
                        :disabled="this.existing_group ? true : false"
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
            <div slot="input" v-if="existing_time_slot">
                <input
                        disabled="true"
                        readonly="true"
                        type="text"
                        class="form-control"
                        v-model="time_slot"
                        :value="time_slot"
                        name="">
            </div>
            <div class="input-group" slot="input" v-else>
                <input  @blur="updateDateTimeEl('time_slot', $event)"
                        type="text"
                        class="time_slot form-control"
                        v-model="time_slot"
                        :value="time_slot"
                        name="">
                <span class="input-group-btn">
                    <button data-toggle="tooltip" title="Clear" data-placement="top" class="btn white" style="padding-bottom:5px" @click.prevent="clearTimeSlot"><i class="fa fa-times-circle-o" aria-hidden="true"></i></button>
                </span>
            </div>
            <span class="text-xs text-muted" slot="text-help">(optional)</span>
            <span class="text-xs text-muted block" slot="text-help">Assign a time slot for when the group's items will post to the flashsale</span>
        </inline-field>
    </span>
</template>
<style src="./../multiselect/vue-multiselect.min.css"></style>
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
            existing_group: {
                type: Object
            },
            existing_time_slot:{

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
        mounted(){
            if(this.existing_time_slot){
                this.time_slot = moment(this.existing_time_slot).format('MM/DD/YYYY hh:mma');
            }
            if(this.existing_group){
                this.group = this.existing_group;
            }
            this.registerDateTimePicker();
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
            clearTimeSlot(){
                this.time_slot = null;
                $('#group_'+this.id+' .time_slot').val('').trigger('change');
            },
            updateDateTimeEl(option, event){
                const $el = $(event.target);
                this.$data[option] = $el.val();
            },
            registerDateTimePicker(){
                this.$nextTick(function(){
                    let minDate = moment().add('1', 'hour');
                    let options = {
                        format: "MM/DD/YYYY hh:mma",
                        allowInputToggle: true,
                        useCurrent: false,
                        sideBySide: true,
                        defaultDate: minDate,
                        minDate: minDate,
                        icons: {
                            time: 'fa fa-clock-o fa-lg',
                            clear: 'fa fa-times-circle-o',
                            date: 'fa fa-lg fa-calendar',
                            up: 'fa fa-chevron-up',
                            down: 'fa fa-chevron-down',
                            previous: 'fa fa-chevron-left',
                            next: 'fa fa-chevron-right'
                        }
                    };

                    $('#group_'+this.id+' input.time_slot').datetimepicker(options);
                    $('#group_'+this.id+' .time_slot').val('').trigger('change');
                });
            },
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