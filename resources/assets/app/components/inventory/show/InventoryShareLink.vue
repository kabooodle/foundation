<template>
    <div>
        <modal modal_id="share--modal" :display_footer="0" :use_header_close="true">
            <template slot="modal_header">Share various link types to this item</template>
            <template class="clearfix" slot="modal_body">
                <div class="form-group" id="link-create--wrapper">

                    <template v-if="!actions.create">
                        <div  class="text-center center-block">
                            <button @click="createLink" class="btn white">Create custom link</button>
                        </div>
                    </template>

                    <template v-if="actions.create">
                        <div class="input-group">
                            <input
                                    :disabled="actions.saving"
                                    :class="actions.saving ? 'disabled' : null"
                                    :maxlength="link_name_length_limit"
                                    type="text"
                                    class="form-control"
                                    :value="link_name_filter"
                                    v-model="link_name_filter"
                                    placeholder="Name your link (i.e. live-sale-Jan-12-2016)">
                            <div class="input-group-btn">
                                <button :disabled="actions.saving" :class="actions.saving ? 'disabled' : null" class="btn white b-r-0" type="button" @click.prevent="cancelCreateLink">
                                    <small class="small _500">
                                        CANCEL
                                    </small>
                                </button>
                                <button
                                        :disabled="actions.saving || ! is_link_name_dirty"
                                        :class="actions.saving || ! is_link_name_dirty ? 'disabled' : null"
                                        class="btn primary"
                                        type="button"
                                        @click.prevent="saveLinkName">
                                    <small class="small _500">
                                        SAVE
                                        <spinny v-if="actions.saving"></spinny>
                                    </small>
                                </button>
                            </div>
                        </div>
                        <div class="clearfix text-xs text-muted">
                            <span class="t pull-left">
                                ( alphanumeric, dashes, and underscores only<template v-if="link_name_characters_remaining">, {{ link_name_characters_remaining }} characters remaining </template>)
                            </span>

                        </div>

                        <div class="input-group m-t-1" v-if="link_name_filter">
                            <input type="text" readonly="" class="form-control readonly-lt has-value" id="live-sale-url"
                                   :value="link_endpoint">
                            <div class="input-group-btn">
                                <button
                                        :class="is_link_name_dirty ? 'disabled' : null"
                                        :disabled="is_link_name_dirty"
                                        data-animation="false"
                                        class="btn white"
                                        data-clipboard-target="#live-sale-url"
                                        type="button" data-original-title="" title="">
                                    <small class="small _500">COPY</small>
                                </button>
                            </div>
                        </div>

                    </template>
                </div>

                <div class="form-group" v-if="!actions.create">
                    <small class="text-sm text-muted text-u-c">Default</small>
                    <div class="input-group">
                        <input type="text" readonly="" class="form-control readonly-lt has-value" id="link-text" placeholder="Search for..." value="http://kabooodle.dev/invite/jaketoolson">
                        <span class="input-group-btn">
                            <button data-animation="false" class="btn white" data-clipboard-target="#link-text" type="button" data-original-title="" title="">
                                <small class="small _500">COPY</small>
                            </button>
                        </span>
                    </div>
                </div>
                <div class="text-center center-block m-t-2">
                    <img src="/assets/images/icons/share-icon.jpg" height="90"/>
                    <small class="m-t-1 block text-sm text-muted">Create and share the link to track source of a claim.</small>
                </div>
            </template>
        </modal>
    </div>
</template>
<script>
    const initial_state = function(){
        return{
            is_dirty: true,
            link_name : null,
            link_name_length_limit : 30,
            previously_saved_name: null,
            actions: {
                create: false,
                saving: false,
                creating: false,
            },
        }
    };
    import Modal from '../../Modal.vue';
    import Spinny from '../../Spinner.vue';
    export default{
        props: {
            shoppable_save_endpoint: {
                type: String,
                required: true,
            },
            shoppable_endpoint: {
                type: String,
                required: true
            },
            shoppable_id: {
                type: String,
                required: true,
            },
            shoppable_uuid: {
                type: String,
                required: true,
            }
        },
        data(){
            return initial_state();
        },
        methods: {
            createLink(){
                this.actions.create = true;
            },
            cancelCreateLink(){
                Object.assign(this.$data, initial_state());
            },
            saveLinkName(){
                this.actions.saving = true;
                const data = {
                    name: this.link_name_filter,
                    id: this.shoppable_id,
                    uuid: this.shoppable_uuid
                }
                this.$http.post(this.shoppable_save_endpoint, data).then((response)=>{
                    this.is_dirty = false;
                    this.previously_saved_name = data.name;
                    notify({
                        type: 'success',
                        text: response.body.data.msg
                    });
                }, (response)=>{
                    this.is_dirty = true;
                    notify({
                        text: response.body.data.msg
                    });
                }).finally(()=>{
                   this.actions.saving = false;
                });
            }
        },
        computed: {
            is_link_name_dirty(){
                if (this.previously_saved_name) {
                    if (this.previously_saved_name === this.link_name_filter) {
                        this.is_dirty = false;
                    } else {
                        this.is_dirty = true;
                    }
                }

                return this.is_dirty;
            },
            link_endpoint(){
                return this.shoppable_endpoint.replace(/::0::/, this.link_name_filter);
            },
            link_name_endpoint(){
                return 'http://kabooodle.dev/c/s/'+this.link_name_filter+'/adeline-6__i';
            },
            link_name_filter: {
                get: function(){

                    // Only allow alpha numeric dash underscore.
                    let temp = this.link_name ? this.link_name.replace(/[^a-z0-9-_]+/g,'') : null;

                    // If length exceeds our limit, return everything starting from 0 to length
                    if (temp && temp.length > this.link_name_length_limit) {
                        return temp.substring(0, this.link_name_length_limit);
                    }

                    return temp;
                },
                set: function(val){
                    this.is_dirty = true;
                    this.link_name = val;
                }
            },
            link_name_characters_remaining(){
                let remaining = this.link_name_length_limit - this.link_name_length;
                return (remaining < 20 ? remaining : false);
            },
            link_name_length(){
                return this.link_name ? this.link_name.length : 0;
            },
        },
        components:{
            'modal' : Modal,
            'spinny' : Spinny,
        }
    }
</script>
