<template>
    <div>
        <div class="box white">
            <div class="box-body">
                <div class="text-center center-block">
                    <button v-if="edit" :class="viewing ? 'disabled' : null" :disabled="viewing" @click.prevent="viewGrouping" class="btn primary btn-sm ">
                        View Outfit <spinny v-if="viewing"></spinny>
                    </button>
                    <button v-if="!edit && multiple" :class="adding ? 'disabled' : null" :disabled="adding" @click.prevent="addGrouping" class="btn primary btn-sm ">
                        Add Outfit <spinny v-if="adding"></spinny>
                    </button>
                    <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="save" class="btn primary btn-sm ">
                        {{ saveOutfitsText }} <spinny v-if="saving"></spinny>
                    </button>
                </div>
            </div>
        </div>
        <div id="inventory-groupings">
            <inventory-grouping v-for="(grouping, index) in groupings"
                :edit=edit
                :key="grouping.id"
                :grouping=grouping
                :inventory=inventoryGrouped
                :restricted-inventory-ids=restrictedInventoryIds
                :s3_key_url="s3_key_url"
                v-on:duplicate-grouping="duplicateGrouping(grouping)"
                v-on:delete-grouping="deleteGrouping(grouping, index)"
                :duplicatable=multiple
            ></inventory-grouping>
        </div>
    </div>
</template>
<script>
    import InventoryGroupingForm from './InventoryGroupingForm.vue'
    import Spinny from '../Spinner.vue';

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
                inventory: [],
                inventoryGrouped: [],
                restrictedInventoryIds: [],
            }
        },
        created: function () {
            this.getInventory();
            if (this.edit) {
                this.setEditGrouping();
            } else {
                this.addGrouping();
            }
        },
        computed: {
            saveInventoryGroupingsEndpoint: function () {
                return this.inventoryGroupingsEndpoint + (this.edit ? '/' + this.editGrouping.id : '');
            },
            saveGroupingsData: function () {
                if (this.edit) {
                    return {
                        'grouping': this.groupings[0],
                    }
                } else {
                    return {
                        'groupings': this.groupings,
                    }
                }
            },
            saveOutfitsText: function () {
                return 'Save Outfit' + (this.groupings.length > 1 ? 's' : '');
            },
        },
        methods: {
            getInventory: function () {
                var self = this;
                self.retrievingInventory = true;
                this.$http.get(this.inventoryEndpoint)
                    .then(function (response) {
                        self.inventory = response.data.data.inventory;
                        self.inventoryGrouped = response.data.data.groupings;
                    }, function (response) {

                    }).finally(()=>{
                        self.retrievingInventory = false;
                });
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
                this.groupings.push({
                    'id': this.getNewId(),
                    'name': null,
                    'description': null,
                    'locked': true,
                    'price_usd': null,
                    'initial_qty': null,
                    'inventory': [],
                    'image': null,
                    'duplicating': false,
                });
                this.adding = false;
            },
            duplicateGrouping: function (grouping) {
                this.groupings.push({
                    'id': this.getNewId(),
                    'name': grouping.name,
                    'description': grouping.description,
                    'locked': grouping.locked,
                    'price_usd': grouping.price_usd,
                    'initial_qty': grouping.initial_qty,
                    'inventory': grouping.inventory,
                    'image': grouping.image,
                    'duplicating': false,
                });
                grouping.duplicating = false;
            },
            setEditGrouping: function () {
                this.groupings.push({
                    'id': this.editGrouping.id.toString(),
                    'name': this.editGrouping.name,
                    'description': this.editGrouping.description,
                    'locked': this.editGrouping.locked,
                    'price_usd': this.editGrouping.price_usd,
                    'initial_qty': this.editGrouping.initial_qty,
                    'inventory': this.editGrouping.inventory_items,
                    'image': this.editGrouping.coverimage,
                    'duplicating': false,
                });
            },
            save: function () {
                if (this.edit ? this.updateGrouping() : this.saveGroupings());
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
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
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
                        notify({
                            'text': 'We\'re sorry. Something went wrong. Please try again.',
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
        },
        components: {
            'inventory-grouping': InventoryGroupingForm,
            'spinny': Spinny,
        },
    }
</script>
