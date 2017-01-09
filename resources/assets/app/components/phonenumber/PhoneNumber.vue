<template>
    <div>
        <spinny class="text-center center-block" :size="'' + 18" v-if="fetching"></spinny>
        <template v-else>
            <template v-if="(exists && phone_object.verified)">
                <p class="">Current number: {{ phone_object.number }} <span data-toggle="tooltip" title="Verified" data-original-title="Verified"><i aria-hidden="true" class="fa fa-check-circle text-success"></i></span>
                    <button v-if="! adding_new" class="btn-link btn-text text-primary text-xs" @click="adding_new = true">Change number</button>
                    <button v-if="adding_new" class="btn-link btn-text text-primary  text-xs" @click="adding_new = false">Cancel</button>
                </p>
            </template>
            <template v-if="adding_new">
                <div class="row clearfix">
                    <div class="col-md-4">
                        <input type="text" name="phone_number" v-model.trim="phone_number" class="form-control " placeholder="10 digit number">
                    </div>
                    <div class="col-md-2">
                        <button
                                @click="sendVerificationCode"
                                type="button"
                                class="btn white btn-sm"
                                :disabled="!phone_number || creating"
                                :class="!phone_number || creating? 'disabled' : null"
                        >
                            Send code <spinny v-if="creating"></spinny>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="phone_number" v-model.trim="verification_code" class="form-control " placeholder="Enter code">
                    </div>
                    <div class="col-md-2">
                        <button
                                @click="verifyCode"
                                type="button"
                                class="btn white btn-sm"
                                :disabled="(!phone_number || !verification_code || verifying)"
                                :class="(!phone_number || !verification_code || verifying ? 'disabled' : null)"
                            >
                            Verify <spinny v-if="verifying"></spinny>
                        </button>
                    </div>
                </div>
            </template>
        </template>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    export default{
        props: {
            fetch_endpoint: {
                required: true,
                type: String
            },
            verify_endpoint: {
                required: true,
                type: String
            },
            create_endpoint: {
                required: true,
                type: String
            }
        },
        data(){
            return{
                fetching: false,
                creating: false,
                verifying: false,
                adding_new: false,

                verification_code: null,
                phone_number: null,
                exists: false,
                phone_object: {
                    verified: false
                }
            }
        },
        created(){
            this.fetchPhoneNumber();
        },
        methods: {
            fetchPhoneNumber(){
                this.fetching = true;
                this.$http.get(this.fetch_endpoint).then((response)=>{
                    let phone_object = response.body.data;
                    if (phone_object != null) {
                        console.log(phone_object);
                        this.phone_object = phone_object;
                        this.exists = true;
                        this.adding_new = false;
                    } else {
                        this.adding_new = true;
                    }
                }).finally(()=>{
                    this.fetching = false;
                });
            },
            sendVerificationCode(){
                this.creating = true;
                this.$http.post(this.create_endpoint, {phone_number: this.phone_number}).then((response)=>{
                    notify({text: response.body.data.msg, type: 'success'});
                }, (response)=>{
                    notify({text: response.body.data.msg});
                }).finally(()=>{
                    this.creating = false;
                });
            },
            verifyCode(){
                this.verifying = true;
                this.$http.put(this.verify_endpoint, {phone_number: this.phone_number, code: this.verification_code}).then((response)=>{
                    notify({text: response.body.data.msg, type: 'success'});
                    this.exists = true;
                    this.phone_object = response.body.data.model;
                    this.verification_code = null;
                    this.phone_number = null;
                    this.adding_new = false;
                }, (response)=>{
                    notify({text: response.body.data.msg});
                }).finally(()=>{
                    this.verifying = false;
                });
            },
        },
        components:{
            'spinny' : Spinny
        }
    }
</script>
