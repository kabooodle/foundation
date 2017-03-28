<template>
    <div>
        <div class="box white">
            <div class="box-header">
                <h2>Create Outfits
                    <a class="text-success onboard-show-btn" @click.prevent="showTourModal">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </a>
                </h2>
                <small>Once an outfit is created, you can add it to any sale, anytime!</small>
                <div class="text-center center-block">
                    <button v-if="edit" :class="viewing ? 'disabled' : null" :disabled="viewing" @click.prevent="viewGrouping" class="btn primary btn-sm ">
                        View Outfit <spinny v-if="viewing"></spinny>
                    </button>
                    <button v-if="!edit && multiple" :class="adding ? 'disabled' : null" :disabled="adding" @click.prevent="addGrouping" class="btn primary btn-sm ">
                        Add Outfit <spinny v-if="adding"></spinny>
                    </button>
                    <button :class="disableSaving ? 'disabled' : null" :disabled="disableSaving" @click.prevent="save" class="btn primary btn-sm ">
                        {{ saveOutfitsText }} <spinny v-if="saving"></spinny>
                    </button>
                </div>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">
                <div id="inventory-groupings">
                    <inventory-grouping v-for="(grouping, index) in groupings"
                        :edit=edit
                        :key="grouping.id"
                        :grouping=grouping
                        :validation-errors=validationErrors[grouping.id]
                        :inventory=inventory
                        :restricted-inventory-ids=restrictedInventoryIds
                        :s3_bucket="s3_bucket"
                        :s3_acl="s3_acl"
                        :s3_key_url="s3_key_url"
                        v-on:duplicate-grouping="duplicateGrouping(grouping)"
                        v-on:delete-grouping="deleteGrouping(grouping, index)"
                        v-on:validated="updateValidationStatus"
                        :duplicatable=multiple
                        :retrieving-inventory=retrievingInventory
                        :show-errors=showErrors
                        :validating=validating
                    ></inventory-grouping>
                </div>
            </div>
        </div>
        <modal modal_id="tour-modal" modal_class="tour-modal" :display_header="false" :display_footer="false">
            <div slot="modal_body">
                <div class="m-a-2 p-a-2 center text-center block-center center-block">
                    <img src="/assets/images/kit1.png">
                    <h5 class="m-t-3 m-b-3">Hi. I'm Kit. If you need help, I'm here to help guide you anytime.</h5>
                    <button type="button" class="m-l-1 btn btn-lg white" @click.prevent="noTour">No thanks, Kit!</button>
                    <button type="button" class="btn btn-success btn-lg" @click.prevent="startTour">Yes, take a tour!</button>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
    import InventoryGroupingForm from './InventoryGroupingForm.vue'
    import Spinny from '../Spinner.vue';
    import Modal from '../Modal.vue';
    import onboard from './inventory-grouping-onboard';

    export default {
        props: {
            inventoryGroupingsEndpoint: {
                type: String,
                required: true,
            },
            inventoryEndpoint: {
                type: String,
                required: true,
            },
            s3_bucket: {
                type: String,
                required: true,
            },
            s3_acl: {
                type: String,
                required: true,
            },
            s3_key_url: {
                type: String,
                required: true,
            },
            edit: {
                type: Number,
                required: true,
            },
            editGrouping: {
                type: Object,
                required: false,
            },
            inventoryGroupingsIndexRoute: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                multiple: false,
                adding: false,
                saving: false,
                viewing: false,
                viewRoute: this.inventoryGroupingsIndexRoute + '/' + this.editGrouping.obfuscate_id,
                ids: [],
                groupings: [],
                validationErrors: {},
                retrievingInventory: false,
                inventory: {
                    'raw': [],
                    'groups': [],
                },
                restrictedInventoryIds: [],
                showErrors: false,
                validating: false,
                allGroupingInputIsValid: false,
            }
        },
        created: function () {
            this.getInventory();
            if (this.edit) {
                this.setEditGrouping();
            } else {
                this.addGrouping();
            }
            $Bus.$on('tour:changed', function (stepNumber, step) {
                if (stepNumber == 8) {
                    console.log('scroll up');
                }
            });
        },
        computed: {
            saveInventoryGroupingsEndpoint: function () {
                return this.inventoryGroupingsEndpoint + (this.edit ? '/' + this.editGrouping.id : '');
            },
            saveGroupingsData: function () {
                if (!this.multiple || this.edit) {
                    return this.groupings[0];
                } else {
                    return {
                        'groupings': this.groupings,
                    }
                }
            },
            saveOutfitsText: function () {
                return 'Save Outfit' + (this.groupings.length > 1 ? 's' : '');
            },
            disableSaving: function () {
                return false;
                //return this.saving || this.preventSaving;
            },
            preventSaving: function () {
                var prevent = false;
                this.groupings.forEach(function (grouping) {
                    if (grouping.name == '' || grouping.exceedsAvailableQty || grouping.initial_qty == 0 || grouping.inventory.length == 0 || !grouping.image)
                        prevent = true;
                });
                return prevent;
            },
        },
        methods: {
            getInventory: function (endpoint) {
                var self = this;
                self.retrievingInventory = true;
                this.$http.get(endpoint ? endpoint : this.inventoryEndpoint)
                    .then(function (response) {

                        let pagination = response.body.data.meta;
                        if (response.body.data.data.length > 0) {
                            _.each(response.body.data.data, (groupings)=> {
                                self.inventory.groups.push(groupings);
                            });
                        }

                        self.retrievingInventory = false;
                        if (pagination.next_page_url) {
                            this.getInventory(pagination.next_page_url);
                        } else {
                            self.inventory.groups = _.sortBy(self.inventory.groups, ['name']);
                        }
                    }, function (response) { });
            },
            showTourModal: function () {
                $('#tour-modal').modal('show');
            },
            startTour: function () {
                $(document).find('.tour-modal').modal('hide');
                $(document).find('.onboard-show-btn').hide();
                onboard().start();
            },
            noTour: function () {
                $(document).find('.tour-modal').modal('hide');
            },
            getNewId: function () {
                var possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

                do {
                    var text = '';
                    for (var i=0; i < 5; i++) {
                        text += possible.charAt(Math.floor(Math.random() * possible.length));
                    }
                } while (this.ids.indexOf(text) !== -1);

                this.ids.push(text);

                return text;
            },
            addGrouping: function () {
                this.adding = true;
                let id = this.getNewId();
                this.validationErrors[id] = {
                    'name': {
                        'status': false,
                        'message': '',
                    },
                    'price_usd': {
                        'status': false,
                        'message': '',
                    },
                    'initial_qty': {
                        'status': false,
                        'message': '',
                    },
                    'inventory': {
                        'status': false,
                        'message': '',
                    },
                    'image': {
                        'status': false,
                        'message': '',
                    }
                };
                this.groupings.push({
                    'id': id,
                    'name': null,
                    'description': null,
                    'locked': true,
                    'price_usd': Number(0).toFixed(2),
                    'initial_qty': null,
                    'inventory': [],
                    'image': null,
                    'duplicating': false,
                    'auto_add': true,
                    'max_quantity': true,
                });
                this.adding = false;
            },
            duplicateGrouping: function (grouping) {
                let id = this.getNewId();
                this.validationErrors[id] = {
                    'name': {
                        'status': this.validationErrors[grouping.id.toString()].name.status,
                        'message': this.validationErrors[grouping.id.toString()].name.message,
                    },
                    'price_usd': {
                        'status': this.validationErrors[grouping.id.toString()].price_usd.status,
                        'message': this.validationErrors[grouping.id.toString()].price_usd.message,
                    },
                    'initial_qty': {
                        'status': this.validationErrors[grouping.id.toString()].initial_qty.status,
                        'message': this.validationErrors[grouping.id.toString()].initial_qty.message,
                    },
                    'inventory': {
                        'status': this.validationErrors[grouping.id.toString()].inventory.status,
                        'message': this.validationErrors[grouping.id.toString()].inventory.message,
                    },
                    'image': {
                        'status': this.validationErrors[grouping.id.toString()].image.status,
                        'message': this.validationErrors[grouping.id.toString()].image.message,
                    }
                };
                this.groupings.push({
                    'id': id,
                    'name': grouping.name,
                    'description': grouping.description,
                    'locked': grouping.locked,
                    'price_usd': grouping.price_usd,
                    'initial_qty': grouping.initial_qty,
                    'inventory': grouping.inventory,
                    'image': grouping.image,
                    'duplicating': false,
                    'auto_add': grouping.auto_add,
                    'max_quantity': grouping.max_quantity,
                });
                grouping.duplicating = false;
            },
            setEditGrouping: function () {
                let id = this.editGrouping.id.toString();
                this.validationErrors[id] = {
                    'name': {
                        'status': false,
                        'message': '',
                    },
                    'price_usd': {
                        'status': false,
                        'message': '',
                    },
                    'initial_qty': {
                        'status': false,
                        'message': '',
                    },
                    'inventory': {
                        'status': false,
                        'message': '',
                    },
                    'image': {
                        'status': false,
                        'message': '',
                    }
                };
                this.groupings.push({
                    'id': id,
                    'name': this.editGrouping.name,
                    'description': this.editGrouping.description,
                    'locked': this.editGrouping.locked,
                    'price_usd': this.editGrouping.price_usd,
                    'initial_qty': this.editGrouping.initial_qty,
                    'inventory': this.editGrouping.inventory_items,
                    'image': this.editGrouping.coverimage,
                    'duplicating': false,
                    'auto_add': this.editGrouping.auto_add,
                    'max_quantity': this.editGrouping.max_quantity,
                });
            },
            save: function () {
                this.validateGroupingsInput();
                if (this.allGroupingInputIsValid) {
                    this.showErrors = false;
                    this.validating = false;
                    if (this.edit ? this.updateGrouping() : this.saveGroupings());
                } else {
                    this.showErrors = true;
                    notify({
                        'text': 'There appears to be an issue with your input. Please correct the issue(s) an try again.',
                        'type': 'error'
                    });
                }
            },
            validateGroupingsInput: function () {
                this.validating = true;
            },
            saveGroupings: function () {
                var self = this;
                self.saving = true;
                this.$http.post(this.saveInventoryGroupingsEndpoint, this.saveGroupingsData)
                    .then(function (response) {
                        notify({
                            'text': 'Your outfit' + (this.groupings.length > 1 ? 's were' : ' was') +' saved!',
                            'type': 'success'
                        });
                        confirmModal(function () {
                            $.noty.closeAll();
                            self.reset();
                        }, function () {
                            $.noty.close();
                            window.location.href = self.inventoryGroupingsIndexRoute;
                        }, {text: 'Would you like to create another outfit?'});
                    }, function (response) {
                        self.showErrors = true;
                        self.handleValidationErrorsResponse(response);
                        notify({
                            'text': response.body.data.msg,
                            'type': 'error'
                        });
                    }).finally(()=>{
                        self.saving = false;
                });
            },
            updateGrouping: function () {
                var self = this;
                self.saving = true;
                this.$http.put(this.saveInventoryGroupingsEndpoint, this.saveGroupingsData)
                    .then(function (response) {
                        notify({
                            'text': 'Your outfit was updated!',
                            'type': 'success'
                        });
                    }, function (response) {
                        self.showErrors = true;
                        self.handleValidationErrorsResponse(response);
                        notify({
                            'text': response.body.data.msg,
                            'type': 'error'
                        });
                    }).finally(()=>{
                        self.saving = false;
                });
            },
            reset: function () {
                this.inventory = [];
                this.inventoryGrouped = [];
                this.getInventory();
                this.groupings = [];
                this.addGrouping();
                this.allGroupingInputIsValid = false;
            },
            viewGrouping: function () {
                this.viewing = true;
                window.location.href = this.viewRoute;
            },
            deleteGrouping: function (grouping, index) {
                if (this.edit) {
                    var self = this;
                    this.$http.delete(this.inventoryGroupingsEndpoint+'/'+grouping.id)
                        .then(function (response) {
                            notify({
                                'text': 'Your outfit has been deleted!',
                                'type': 'success'
                            });
                            window.location.href = this.inventoryGroupingsIndexRoute;
                        }, function (response) {
                            notify({
                                'text': 'There was a problem deleting your outfit. Please try again.',
                                'type': 'error'
                            });
                        });
                } else {
                    this.groupings.splice(index, 1);
                }
            },
            updateValidationStatus: function (e) {
                this.allGroupingInputIsValid = e.status;
            },
            handleValidationErrorsResponse: function (response) {
                if (response.body.data.validationErrors) {
                    for (var input in response.body.data.validationErrors) {
                        this.validationErrors[this.groupings[0].id][input].status = true;
                        this.validationErrors[this.groupings[0].id][input].message = response.body.data.validationErrors[input][0];
                    }
                }
            },
        },
        components: {
            'inventory-grouping': InventoryGroupingForm,
            'spinny': Spinny,
            'modal': Modal,
        },
    }
</script>
