<template>
    <div class="form-group row">
        <div class="col-sm-6">
            <input v-if="isInput" type="text" :name="name" :value="address" class="form-control">
            <p v-else>{{ address }}</p>
        </div>
        <div class="col-sm-3">
            <div v-if="isPrimary">
                Primary
            </div>
            <div v-else>
                Make Primary
            </div>
        </div>
        <div class="col-sm-3">
            <div v-if="isVerified">
                Verified
            </div>
            <div v-else>
                Send Verification Email
            </div>
        </div>
    </div>
</template>
<style>

</style>
<script>
    export default {
        props: {
            isInput: {
                type: Boolean,
                default: false,
            },
            name: {
                type: String,
                required: false,
            },
            id: {
                type: String,
                required: false,
            },
            address: {
                type: String,
                required: false,
            },
            isPrimary: {
                type: Boolean,
                default: false,
            },
            isVerified: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {

            }
        },
    }
</script>
