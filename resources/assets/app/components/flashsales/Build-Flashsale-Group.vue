<template>
    <span>
        <modal
                size="modal-lg"
                modal_id="abc"
        >
            <h4 slot="modal_header">Create a group of sellers</h4>
            <template slot="modal_body">
                <div class="error_container block r"></div>
                <template v-if="!saved_notice">
                    <p>Creating a group of sellers allows you to easily invite the group to participate as sellers to any flashsale, vs adding sellers one by one.  You can "reuse" groups anytime!</p>
                    <inline-field :errors="form_errors.name">
                        <template slot="label">Seller group name</template>
                        <input slot="input" type="text" v-model="name" class="form-control">
                    </inline-field>

                    <inline-field :errors="form_errors.users">
                        <template slot="label">Add sellers</template>
                            <multiselect
                                    slot="input"
                                    id="admins_el"
                                    label="username"
                                    track-by="id"
                                    placeholder=""
                                    :options="search_members"
                                    :multiple="true"
                                    :searchable="true"
                                    :loading="isSearching"
                                    :internal-search="false"
                                    :clear-on-select="true"
                                    :close-on-select="true"
                                    :options-limit="10"
                                    :limit="10"
                                    v-model="users"
                                    @search-change="searchMembers">
                                <template slot="option" scope="props">
                                    <div class="option__desc">
                                        <span class="option__title">{{ props.option.username }}</span>
                                        <small class="option__small text-muted text-small">({{ props.option.full_name }})</small>
                                    </div>
                                </template>
                            </multiselect>
                    </inline-field>
                </template>
                <template v-if="saved_notice">
                    {{ saved_notice }}
                </template>
            </template>
            <template slot="modal_footer">
                <template v-if="!saved_notice">
                    <button type="button" :disabled="isSaving" :class="isSaving ?'disabled' :null" class="btn btn-sm primary" @click.prevent="saveGroup">Save <spinny v-if="isSaving"></spinny></button>
                    <button type="button" :disabled="isSaving" :class="isSaving ?'disabled' :null" class="btn btn-sm white" @click.prevent="closeModal">Cancel</button>
                </template>
                <template v-if="saved_notice">
                    <button type="button" class="btn white btn-sm" @click.prevent="closeModal">Close</button>
                </template>
            </template>
        </modal>
    </span>
</template>
<style src="./../multiselect/vue-multiselect.min.css"></style>
<script>
    function initialState (){
        return {
            form_errors: {},
            saved_notice: null,
            isSearching: false,
            isSaving: false,
            search_members: [],
            users: [],
            name: null,
        }
    }
    import InlineForm from '../InlineField.vue';
    import Modal from '../Modal.vue';
    import Multiselect from 'vue-multiselect';
    import Spinny from '../Spinner.vue';
    export default{
        props: {
            s3_bucket: {
                required: true,
                type: String
            },
            s3_acl: {
                required: true,
                type: String
            },
            s3_key_url: {
                required: true,
                type: String
            },
            search_endpoint: {
                required: true,
                type: String
            },
            save_endpoint: {
                required: true,
                type: String
            },
        },
        data(){
            return initialState()
        },
        created(){
            $Bus.$on('build-group:click', ()=>{
                this.openModal();
            });
        },
        computed: {
            data_dirty(){
                return ! (JSON.stringify(this.$data) === JSON.stringify(initialState()));
            }
        },
        methods: {
            resetState(){
                Object.assign(this.$data, initialState())
            },
            openModal(){
                $('#abc').modal('show');
            },
            closeModal(ignoreState){
                if (ignoreState === true || ! this.data_dirty) {
                    $('#abc').modal('hide');
                    this.resetState();
                } else {
                    confirmModal(($noty)=>{
                        $('#abc').modal('hide');
                        this.resetState();
                        $noty.close();
                    })
                }
            },
            searchMembers(query){
                if (query.trim() == '') {
                    return;
                }
                this.isSearching = true;
                this.$http.post(this.search_endpoint, {q: query},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.search_members = response.body.data.data;
                    this.isSearching = false;
                });
            },
            saveGroup(){
                this.isSaving = true;
                this.form_errors = [];
                let userIds = _.pluck(this.users, 'id');

                let payload = {
                    name: this.name,
                    users: userIds
                };

                this.$http.post(this.save_endpoint, payload).then((response) => {
                    let notice = 'Group '+this.name+' successfully created!';
                    notify({text: notice, type: 'success'});
                    setTimeout(()=>{
                        let ignoreState = true;
                        this.closeModal(ignoreState);
                    }, 500);
                }, (response)=>{
                    if (response.body.data.errors) {
                        this.form_errors = response.body.data.errors
                    }
                    this.isSaving = false;
                });
            },
        },
        components: {
            'inline-field' : InlineForm,
            'modal' : Modal,
            'multiselect' : Multiselect,
            'spinny' : Spinny
        },
    }
</script>
