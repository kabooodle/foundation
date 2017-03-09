<template>
    <div class="form-group row">
        <div class="col-sm-8">
            <span>
                <strong>{{ address.name }}</strong>
                <span class="block">{{ address.street1 }}, {{ address.city }}, {{ address.state }}, {{ address.zip }} {{ address.phone }}</span>
            </span>
        </div>
        <div class="col-sm-3">
            <div v-show="isPrimary">
                <div class="text-primary text-center">Primary</div>
            </div>
            <div v-show="!isPrimary">
                <button
                        :disabled="makingprimary"
                        :class="makingprimary ? 'disabled' : null"
                        @click="makePrimary" class="btn white btn-sm btn-block p-x-md">
                    Make Primary <spinny v-if="makingprimary"></spinny>
                </button>
            </div>
        </div>
        <div class="col-sm-1">
            <span v-show="!isPrimary" @click="destroy">
                <i class="fa fa-times text-danger" aria-hidden="true"></i>
            </span>
        </div>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';
    export default {
        props: {
            address: {
                type: Object,
                required: true,
            },
            showType: {
                type: Boolean,
                default: true,
            },
            primaryId: {
                type: Number,
                required: true,
            },
            addressesEndpoint: {
                type: String,
                required: true,
            },
            updatePrimaryEndpoint: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                makingprimary : false
            }
        },
        computed: {
            isPrimary: function () {
                return this.primaryId == this.address.id
            },
            updatePrimaryData: function () {
                return {
                    'type': this.address.type,
                    'address_id': this.address.id,
                }
            },
        },
        methods: {
            makePrimary: function () {
                this.makingprimary = true;
                this.$http.put(this.updatePrimaryEndpoint, this.updatePrimaryData)
                    .then(function (response) {
                        this.$emit('new-primary', this.address.id);
                        notify({
                            'text': 'Your primary address has been updated!',
                            'type': 'success'
                        });
                    }, function (response) {
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    }).finally(()=>{
                        this.makingprimary = false;
                });
            },
            destroy: function () {
                var self = this;
                confirmModal(function () {
                    self.$http.delete(self.addressesEndpoint+'/'+self.address.id)
                    .then(function (response) {
                        $.noty.closeAll();
                        self.$emit('remove-address');
                        notify({
                            'text': 'That address has been deleted.',
                            'type': 'success'
                        });
                    }, function (response) {
                        $.noty.closeAll();
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
                            'type': 'error'
                        });
                    });
                }, function () {
                    $.noty.close();
                }, {text: 'Are you sure you want to delete this address?'});
            }
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
