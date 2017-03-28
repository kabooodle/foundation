<template>
    <div>
        <div class="box" id="create_flashsale_container">
            <div class="box-header">
                <h2>{{ title }}
                    <a class="text-success onboard-show-btn" @click.prevent="showTourModal">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </a>
                </h2>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">
                <div class="cover-photo-wrapper item m-b-1" v-if="cover_photo">
                    <div class="item-overlay active p-a">
                        <span v-if="privacy=='private'" class="pull-left text-u-c label danger label-md">{{ privacy }}</span>
                    </div>
                    <div
                            class="coverimage FlexEmbed FlexEmbed--3by1"
                            :style="'background-image: url('+cover_photo.location+')'">
                    </div>
                    <div class="item-overlay-bottom p-a" v-if="name"><h2>{{ name }}</h2></div>
                </div>

                <inline-field :errors="form_errors.cover_photo">
                    <template slot="input">
                        <input type="hidden" name="cover_photo" v-model="cover_photo" :value="cover_photo ? cover_photo.json : null">
                        <image-attach
                            :s3_bucket="s3_bucket"
                            :s3_acl="s3_acl"
                            :s3_key_url="s3_key_url"
                            multiple="false"
                            :button_title="cover_photo ? 'Replace cover photo' : 'Add cover photo'"></image-attach>
                    </template>
                    <small slot="text-help" class="text-sm text-muted">
                        Recommended dimensions are <u>220px</u> tall by <u>850px</u> wide.
                    </small>
                </inline-field>

                <inline-field :errors="form_errors.name">
                    <template slot="label">Name</template>
                    <template slot="input">
                        <input class="form-control" name="name" v-model="name" type="text">
                    </template>
                </inline-field>

                <inline-field :errors="form_errors.description">
                    <template slot="label">Description</template>
                    <template slot="input">
                        <textarea class="form-control" v-model="description" name="description"></textarea>
                    </template>
                    <template slot="text-help">
                        <small class="text-sm text-muted">(optional)</small>
                    </template>
                </inline-field>

                <inline-field :errors="form_errors.starts_at">
                    <template slot="label">Starting date</template>
                    <template slot="input" v-if="hidden_form_data.starts_at">
                        <input type="text"
                               disabled="true"
                               readonly="true"
                               v-model="starts_at"
                               id="starts_at" name="starts_at" class="form-control">
                    </template>
                    <template slot="input" v-else>
                        <input type="text" @blur="updateDateTimeEl('starts_at', $event)" id="starts_at" name="starts_at" v-model="starts_at" class="needs-datetimepicker form-control">
                    </template>
                </inline-field>

                <inline-field :errors="form_errors.ends_at">
                    <template slot="label">Ending date</template>
                    <template slot="input" v-if="hidden_form_data.ends_at">
                        <input type="text"
                               disabled="true"
                               readonly="true"
                               id="ends_at" name="ends_at" v-model="ends_at" class="form-control">
                    </template>
                    <template slot="input" v-else>
                        <input type="text"
                               @blur="updateDateTimeEl('ends_at', $event)" id="ends_at" name="ends_at" v-model="ends_at" class="needs-datetimepicker form-control">
                    </template>
                </inline-field>

                <inline-field>
                    <template slot="label">Admins</template>
                    <template slot="input">
                        <multiselect
                                id="admins_el"
                                label="username"
                                track-by="id"
                                placeholder=""
                                :options="search_admins"
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
                                    <span class="option__title">{{ props.option.username }} ({{ props.option.full_name}})</span>
                                </div>
                            </template>
                        </multiselect>
                    </template>
                    <template slot="text-help">
                        <small class="text-sm text-muted">Admins are allowed to make changes to this flash sales'
                            settings.
                        </small>
                    </template>
                </inline-field>

                <inline-field :errors="form_errors.privacy">
                    <template slot="label">Privacy</template>
                    <template slot="input">
                        <div class="radio" v-for="privacy_type in privacy_options">
                            <label class="">
                                <input class="has-value" name="privacy" v-model="privacy" type="radio"
                                       :value="privacy_type.privacy">
                                {{ privacy_type.privacy }}
                                <span class="block text-xs text-muted">{{ privacy_type.help }}</span>
                            </label>
                        </div>
                    </template>
                </inline-field>
            </div>
        </div>

        <div v-for="(seller, $index) in sellers_groups_containers" :key="seller.id">
            <div class="box white p-a p-t-2 p-b-2 p-b-0">
                <button
                        @click.prevent="removeSellerContainer(seller, $event)"
                        type="button"
                        style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0; opacity:.3"
                        class=" m-l-1 pull-right btn white btn-xs text-muted ">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
                <seller-group
                        :existing_time_slot="seller.selected_group ? seller.selected_group.pivot.time_slot : null"
                        :id="$index"
                        :existing_group="seller.selected_group ? seller.selected_group : null"
                        :selected_groups="selected_groups"
                        :search_endpoint="group_search_endpoint"
                ></seller-group>
            </div>
        </div>

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-9">
                <button id="btn-add-sellersgroup" type="button" :disabled="isSaving || isDeleting" :class="isSaving || isDeleting ? 'disabled' : null" @click.prevent="addSellerContainer(null,null, $event)" class="btn white">Add sellers group</button>
                <button type="button" :disabled="isSaving || isDeleting" :class="isSaving || isDeleting ? 'disabled' : null" class="btn primary" @click.prevent="saveFlashsale">
                    <template v-if="isEditing">Update and Save</template>
                    <template v-else>Save</template>
                    <spinny v-if="isSaving"></spinny>
                </button>
                <button v-if="isEditing" type="button" :disabled="isSaving || isDeleting" :class="isSaving || isDeleting ? 'disabled' : null" class="btn warning"
                        @click.prevent="deleteFlashsale">
                    Delete <spinny v-if="isDeleting" color="white"></spinny>
                </button>
            </div>
        </div>

        <build-group
                :s3_bucket="s3_bucket"
                :s3_acl="s3_acl"
                :s3_key_url="s3_key_url"
                :search_endpoint="search_endpoint"
                :save_endpoint="group_save_endpoint"
        ></build-group>


        <modal modal_id="tour-modal" modal_class="tour-modal" :display_header="false" :display_footer="false">
            <div slot="modal_body">
                <div class="m-a-2 p-a-2 center text-center block-center center-block">
                    <img src="/assets/images/kit1.png">
                    <h5 class="m-t-3 m-b-3">I'm Kit. If you need help, I'm here to help guide you anytime.</h5>
                    <button type="button" class="m-l-1 btn btn-lg white" @click.prevent="noTour">No thanks, Kit!</button>
                    <button type="button" class="btn btn-success btn-lg" @click.prevent="startTour">Yes, take a tour</button>
                </div>
            </div>
        </modal>

    </div>
</template>
<style src="./../multiselect/vue-multiselect.min.css"></style>
<script>
    import BuildGroup from './Build-Flashsale-Group.vue';
    import FileUpload from '../FileUpload.vue';
    import InlineField from '../InlineField.vue';
    import Multiselect from 'vue-multiselect';
    import Modal from '../Modal.vue';
    import onboard from './onboard';
    import SellerGroup from './Seller-Group.vue';
    import Spinny from  '../Spinner.vue';

    const PRIVACY_PRIVATE = 'private';
    const PRIVACY_PUBLIC = 'public';

    function initialState(){
        return {
            // Form data
            name: null,
            admins: [],
            cover_photo: null,
            starts_at: null,
            ends_at: null,
            description: null,
            privacy: PRIVACY_PUBLIC,
            selected_groups: [],
            seller_groups: [],

            hidden_form_data: {},

            form_errors: [],
            loading: {
                admins: false,
                sellers: false,
            },
            isDeleting: false,
            isEditing: false,
            isSaving: false,
            privacy_options: [
                {
                    privacy: PRIVACY_PUBLIC,
                    help: 'Accessible to anyone browsing.',
                },
                {
                    privacy: PRIVACY_PRIVATE,
                    help: 'Accessible only to admins, sellers and anyone with the link.',
                },
            ],
            previousRequest: null,
            search_admins: [],
            sellers_groups_containers: [],
        }
    }

    export default{
        props: {
            title:{
                type: String,
                default: 'Create a flash sale'
            },
            groups_endpoint: {
                type: String
            },
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
            save_endpoint: {
                required: true,
                type: String
            },
            search_endpoint: {
                required: true,
                type: String
            },
            group_search_endpoint: {
                required: true,
                type: String
            },
            group_save_endpoint: {
                required: true,
                type: String
            },
            existing_data: {
                type: Object,
                default (){
                    return {};
                }
            },
            fetch_model_endpoint: {
                type: String
            },
            fetch_model: {
                type: Boolean
            },
        },
        data(){
            return initialState()
        },
        mounted(){
            this.registerDateTimePicker();
            this._checkIfExistingData();
        },
        computed: {},
        created(){
            $Bus.$on('flashsale:component:data', (data)=>{
                const seller_group_index = this.seller_groups.indexOf(data);
                if (seller_group_index > -1) {
                    this.seller_groups.splice(seller_group_index, 1);
                }
                this.seller_groups.push(data);
            });

            $Bus.$on('seller_container:add', ()=> {
                this.addSellerContainer();
            });

            $Bus.$on('image:uploaded', (el, data)=> {
                this.cover_photo = data;
            });

            $Bus.$on('flashsale:data:request', ()=> {
                $Bus.$emit('flashsale:data:payload', this.$data);
            });

            $Bus.$on('sellergroup:selected', (group, containerId)=> {

                // When a group inside a seller container is selected,
                // this event is fired.  We handle this group by "assigning" it to
                // the container wrapper, just for reference.

                // Already exists? Remove it and re-add :)
                const index = this.selected_groups.indexOf(group.id);
                if (index > -1) {
                    this.selected_groups.splice(index, 1);
                }

                this.selected_groups.push(group);

                // Add the group to the corresponding container.
                // This keeps the group associated to the container and a 1 to 1.
                this.sellers_groups_containers[containerId].selected_group = group;
            });
        },
        methods: {
            _checkIfExistingData(){
                if (this.existing_data && _.size(this.existing_data) > 0 ) {
                    this.isEditing = true;

                    let data = this.existing_data;

                    this.hidden_form_data.starts_at = true;
                    this.hidden_form_data.ends_at = true;

                    this.name = data.name;
                    this.admins = data.admins;
                    this.starts_at = moment(data.starts_at).format('MM/DD/YYYY hh:mma');
                    this.ends_at = moment(data.ends_at).format('MM/DD/YYYY hh:mma')
                    this.description = data.description;
                    this.privacy = data.privacy;
                    this.cover_photo = data.coverimage;

                    if (data.seller_groups.length > 0){
                        _.each(data.seller_groups, (k,b)=>{
                            this.addSellerContainer(k.id, k);
                            this.selected_groups.push(k);
                        });
                    }

//
//                            cover_photo: null,
//                            selected_groups: [],
//                            seller_groups: [],


                }
            },
            /**
             * User when the date fields are blurred to actually set the data.
             * @param option
             * @param event
             */
            updateDateTimeEl(option, event){
                const $el = $(event.target);
                this.$data[option] = $el.val();
            },
            registerDateTimePicker(){
                this.$nextTick(function(){
                    let minDate = moment().add('5', 'minute');
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

                    $('input.needs-datetimepicker').datetimepicker(options);

                    // hack for clearing the inputs, except, checkbox.
                    $('.needs-datetimepicker').val('').trigger('change');

                    $("#starts_at").on("dp.change", function (e) {
                        $('#ends_at').data("DateTimePicker").minDate(e.date);
                    });
                    $("#ends_at").on("dp.change", function (e) {
                        $('#starts_at').data("DateTimePicker").maxDate(e.date);
                    });
                });
            },
            resetState(){
                Object.assign(this.$data, initialState());
                $('.needs-datetimepicker').val('').trigger('change');
            },
            searchAdmins(query){
                if (query.trim() == '') {
                    return;
                }
                this.loading.admins = true;
                this.$http.post(this.search_endpoint, {q: query}, {
                    before(request) {
                        if (this.previousRequest) {
                            this.previousRequest.abort();
                        }
                        this.previousRequest = request;
                    }
                }).then((response) => {
                    this.search_admins = response.body.data.data;
                    this.loading.admins = false;
                });
            },
            addSellerContainer(id, selected_group, $event){
                id = id ? id : randomAlphaStr(4);
                selected_group = selected_group ? selected_group : null;
                this.sellers_groups_containers.push({id: id, selected_group: selected_group});
            },
            removeSellerContainer(container){
                confirmModal(($noty)=> {

                    // We want to look at the selected container and see if a group has been associated to it.
                    // If it does, remove this group from the selected groups array.
                    let associated_group = container.selected_group;

                    // Get the index of the container in the sellers groups containers
                    const container_index = this.sellers_groups_containers.indexOf(container);

                    if (container_index > -1) {
                        // Remove the container
                        this.sellers_groups_containers.splice(container_index, 1);

                        // Did the container have a group associated (selected) to it?
                        // If it did, remove the group from the array.
                        if (associated_group && associated_group.id) {
                            const selected_group_index = this.selected_groups.indexOf(associated_group);
                            this.selected_groups.splice(selected_group_index, 1);
                        }
                    }

                    // Reset some annoying data.
                    this.seller_groups = [];

                    $noty.close();
                })
            },
            saveFlashsale(){
                this.isSaving = true;
                this.form_errors = {};

                $Bus.$emit('flashsale:saving');


                // I have seller groups that may have erroneous data
                // I need to loop over each one and only return the ones that
                // have a matching resource found in sellers_groups_containers
                // HACKY until we can vuex the state.

                if (this.seller_groups_containers && this.seller_groups_containers.length) {
                    const actualIds = _.pluck(_.pluck(this.sellers_groups_containers, 'selected_group'), 'id');
                    this.seller_groups = _.uniq(_.filter(this.seller_groups, (group)=>{
                        return _.contains(actualIds, group.id);
                    }), 'id');

                }

                let method = 'post';
                if (this.isEditing) {
                    method = 'put';
                }

                this.$http[method](this.save_endpoint, this.$data).then((response)=>{
                    notify({
                        text: response.body.data.msg,
                        type: 'success'
                    });
                    if (!this.isEditing){
                        this.resetState();
                    }
                    this.isSaving = false;
                }, (response)=>{
                    this.isSaving = false;
                    if (response.body.hasOwnProperty('data') && response.body.data.hasOwnProperty('errors')) {
                        this.form_errors = response.body.data.errors;
                    }

                    if (response.body.data.hasOwnProperty('msg')) {
                        notify({
                           text: response.body.data.msg
                        });
                    }
                });
            },
            deleteFlashsale(event){
                confirmModal(($noty)=>{
                    $noty.close();
                    this.isDeleting = true;
                    this.$http.delete(this.save_endpoint).then((response)=>{
                        notify({
                            text: response.body.data.msg,
                            type: 'success'
                        });
                        setTimeout(()=>{
                           window.location.href = response.body.data.redirect;
                        }, 1500);
                    }, (response)=>{
                        this.isDeleting = true;
                        if (response.body.data.hasOwnProperty('msg')) {
                            notify({
                                text: response.body.data.msg
                            });
                        }
                    });
                });
            },
            showTourModal(){
                $('#tour-modal').modal('show');
            },
            startTour(){
                $(document).find('.tour-modal').modal('hide');
                $(document).find('.onboard-show-btn').hide();
                onboard().start();
            },
            noTour(){
                $(document).find('.tour-modal').modal('hide');
            },
        },
        components: {
            'build-group': BuildGroup,
            'image-attach': FileUpload,
            'inline-field': InlineField,
            'modal' : Modal,
            'multiselect': Multiselect,
            'seller-group': SellerGroup,
            'spinny': Spinny
        }
    }
</script>
