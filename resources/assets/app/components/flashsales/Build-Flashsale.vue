<template>
    <div>

        <inline-field>
            <template slot="label" class="abc">
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

        <inline-field :errors="form_errors.host">
            <template slot="label">Hosted by</template>
            <template slot="input">
                <select name="host" class="form-control" ></select>
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
    const PRIVACY_PRIVATE= 'private';
    const PRIVACY_PUBLIC = 'public';
    const PRIVACY_SECRET = 'secret';

    import InlineField from '../InlineField.vue';
    import FileUpload from '../FileUpload.vue';
    import Modal from '../Modal.vue';
    import Spinny from  '../Spinner.vue';

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
            user_hash: {
                required: true
            },
            s3_key_url : {
                required: true
            }
        },
        data(){
            return{
                name: null,
                description: null,
                dates:{
                    start_date :null,
                    end_date: null
                },
                cover_photo: null,
                group: {},
                admins: [],
                hosted_by: 'self',
                privacy: PRIVACY_PUBLIC,
                privacy_options: [
                        PRIVACY_PUBLIC,
                        PRIVACY_PRIVATE,
                        PRIVACY_SECRET
                ]
            }
        },
        created(){
            $Bus.$on('image:uploaded', (el, data)=>{
                this.cover_photo = data.location;
            });
        },
        components:{
            'inline-field' : InlineField,
            'spinny' : Spinny,
            'modal' : Modal,
            'image-attach' : FileUpload
        }
    }
</script>
