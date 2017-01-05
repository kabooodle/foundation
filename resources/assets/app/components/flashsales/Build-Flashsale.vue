<template>
    <div>
        <inline-field :errors="form_errors.cover_photo">
            <template slot="label">
                <div class="avatar_container _128 pull-right avatar-thumbnail" v-if="cover_photo">
                    <img :src="cover_photo">
                </div>
            </template>
            <template slot="input">
                <input type="hidden" name="cover_photo" v-model="cover_photo" :value="cover_photo">
                <image-attach
                        :s3_key_url="s3_key_url"
                        multiple="false"
                        :user_hash="user_hash"
                        :button_title="cover_photo ? 'Replace cover photo' : 'Add cover photo'"
                ></image-attach>
            </template>
        </inline-field>

        <inline-field :errors="form_errors.name">
            <template slot="label">Name</template>
            <template slot="input">
                <input class="form-control" name="name"  type="text">
            </template>
        </inline-field>

        <inline-field :errors="form_errors.description">
            <template slot="label">Description</template>
            <template slot="input">
                <textarea class="form-control"  name="description"></textarea>
            </template>
        </inline-field>

        <inline-field :errors="form_errors.starts_at">
            <template slot="label">Starting date</template>
            <template slot="input">
                <input type="text" name="starts_at"  class="form-control">
            </template>
        </inline-field>

        <inline-field :errors="form_errors.ends_at">
            <template slot="label">Ending date</template>
            <template slot="input">
                <input type="text" name="ends_at"  class="form-control">
            </template>
        </inline-field>

        <inline-field :errors="form_errors.hosted_by">
            <template slot="label">Hosted by</template>
            <template slot="input">
                <select name="host" v-model="hosted_by" class="form-control">
                    <option v-for="host_option in host_options" :value="host_option">{{ host_option }}</option>
                </select>
            </template>
        </inline-field>

        <inline-field v-if="hosted_by_group">
            <template slot="label">Admins</template>
            <template slot="input">
                <multiselect
                        id="admins_el"
                        label="full_name"
                        track-by="id"
                        placeholder="Search by name or username"
                        :custom-label="nameWithUsername"
                        :options="admins_list"
                        :multiple="true"
                        :searchable="true"
                        :loading="loading.admins"
                        :internal-search="false"
                        :clear-on-select="true"
                        :close-on-select="true"
                        :options-limit="10"
                        :limit="10"
                        v-model="admins"
                        @search-change="searchAdmins">
                    <template slot="option" scope="props">
                        <div class="option__desc">
                            <span class="option__title">{{ props.option.full_name }}</span>
                            <small class="option__small text-muted text-small">({{ props.option.username }})</small>
                        </div>
                    </template>
                </multiselect>
            </template>
            <template slot="text-help">
                <small class="text-sm text-muted">Users whom can manage the sales' settings</small>
            </template>
        </inline-field>


        <inline-field>
            <template slot="label">Sellers</template>
            <template slot="input">
                <multiselect
                        id="sellers_el"
                        label="full_name"
                        track-by="id"
                        placeholder="Search by name or username"
                        :custom-label="nameWithUsername"
                        :options="sellers_list"
                        :multiple="true"
                        :searchable="true"
                        :loading="loading.sellers"
                        :internal-search="false"
                        :clear-on-select="true"
                        :close-on-select="true"
                        :options-limit="10"
                        :limit="10"
                        v-model="sellers"
                        @search-change="searchSellers">
                    <template slot="option" scope="props">
                        <div class="option__desc">
                            <span class="option__title">{{ props.option.full_name }}</span>
                            <small class="option__small text-muted text-small">({{ props.option.username }})</small>
                        </div>
                    </template>
                </multiselect>
            </template>
        </inline-field>

        <inline-field :errors="form_errors.privacy">
            <template slot="label">Privacy</template>
            <template slot="input">
                <div class="radio" v-for="privacy_type in privacy_options">
                    <label class="">
                        <input class="has-value" name="privacy" v-model="privacy" type="radio" :value="privacy_type">
                        {{ privacy_type }}
                    </label>
                </div>
            </template>
        </inline-field>

        <modal modal_id="flashsale_modal">
            <template slot="modal_header">
                <h6 class="m-b-0">Create group</h6>
            </template>
            <template slot="modal_content">

            </template>
            <template slot="modal_footer">

            </template>
        </modal>
    </div>
</template>
<script>
    import FileUpload from '../FileUpload.vue';
    import InlineField from '../InlineField.vue';
    import Modal from '../Modal.vue';
    import Multiselect from 'vue-multiselect';
    import Spinny from  '../Spinner.vue';

    const HOST_GROUP = 'group';
    const HOST_SELF = 'self';
    const PRIVACY_PRIVATE= 'private';
    const PRIVACY_PUBLIC = 'public';

    export default{
        props: {
            form_errors: {
                default: function () {
                    return {}
                }
            },
            groups_endpoint: {
                type: String
            },
            s3_key_url : {
                required: true,
                type: String
            },
            search_endpoint: {
                required: true,
                type: String
            },
            user_hash: {
                required: true
            },
        },
        data(){
            return{
                admins: [],
                admins_list:[],
                cover_photo: null,
                dates:{
                    start_date :null,
                    end_date: null
                },
                description: null,
                group: {},
                hosted_by: null,
                host_options: [
                        HOST_SELF,
                        HOST_GROUP,
                ],
                loading: {
                    admins: false,
                    sellers: false,
                },
                name: null,
                privacy: PRIVACY_PUBLIC,
                privacy_options: [
                        PRIVACY_PUBLIC,
                        PRIVACY_PRIVATE
                ],
                previousRequest:null,
                sellers: [],
                sellers_list: []
            }
        },
        computed: {
            hosted_by_group(){
                return this.hosted_by === HOST_GROUP;
            }
        },
        created(){
            $Bus.$on('image:uploaded', (el, data)=>{
                this.cover_photo = data.location;
            });
        },
        methods: {
            nameWithUsername ({ full_name, username }) {
                return `${full_name} (${username})`;
            },
            searchAdmins(query){
                if (query.trim() == '') {
                    return;
                }
                this.loading.admins = true;
                this.$http.post(this.search_endpoint, {q: query},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.admins_list = response.body.data.data;
                    this.loading.admins = false;
                });
            },
            searchSellers(query){
                if (query.trim() == '') {
                    return;
                }
                this.loading.sellers = true;
                this.$http.post(this.search_endpoint, {q: query},  {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.sellers_list = response.body.data.data;
                    this.loading.sellers = false;
                });
            },
        },
        components:{
            'modal' : Modal,
            'multiselect' : Multiselect,
            'image-attach' : FileUpload,
            'inline-field' : InlineField,
            'spinny' : Spinny,
        }
    }
</script>
