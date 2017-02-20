<template>
    <div>
        <div class="box white">
            <div class="box-body">
                <div class="col-xs-12">
                    <span class="pull-right" @click="destroy">
                        <spinny v-if="deleting"></spinny>
                        <i v-else class="fa fa-times text-danger pointer" aria-hidden="true"></i>
                    </span>
                    <div style="clear: both"></div>
                </div>
                <div class="col-sm-8">
                    <div class="md-form-group">
                        <input v-model="grouping.name" type="text" name="name" class="md-input">
                        <label>Name</label>
                    </div>
                    <div class="md-form-group">
                        <input v-model="grouping.description" type="text" name="description" class="md-input">
                        <label>Description</label>
                    </div>
                    <div class="md-form-group">
                        <input v-model="grouping.price_usd" type="number" step="0.01" min="0.00" name="price_usd" class="md-input">
                        <label>Price</label>
                    </div>
                    <div class="md-form-group">
                        <input
                            v-model="grouping.initial_qty"
                            type="number"
                            min="0"
                            name="initial_qty"
                            class="md-input">
                        <label>Quantity</label>
                    </div>
                    <div class="md-form-group">
                        <label style="font-size: 0.85em; opacity: 0.5">Inventory</label>
                        <span @click="grouping.locked = !grouping.locked">
                            <i :class="grouping.locked ? 'fa-lock' : 'fa-unlock'" class="fa text-primary pointer"></i>
                        </span>
                        <div class="box">
                            <div :id=attachedInventoryId class="box-body">
                                {{ grouping.inventory.length }}
                            </div>
                        </div>
                        <a @click="showingInventory = !showingInventory">{{ toggleInventoryText }}</a>
                        <small class="text-muted">Qty amounts shown below reflect amounts able to be associated to new outfits.</small>
                        <div v-show="showingInventory">
                            <inventory-group
                                v-for="group in inventory"
                                :key="group.id"
                                :ukey=grouping.id
                                :group=group
                                group_type="inventory"
                                :display_footer_buttons="false"
                                :previously-selected-ids=selectedInventoryIds
                                :use-available-qty=true
                            ></inventory-group>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="clearfix">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="inventory-grouping-image">
                                    <image :src=imageSrc style="width: 100%; height: 100%;"></image>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <span class="add-images-btn">
                                    <file-upload
                                        :ukey=grouping.id
                                        :s3_key_url="s3_key_url"
                                        multiple="false"
                                        :button_title="grouping.image ? 'Replace Image' : 'Add Image'"
                                    ></file-upload>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!edit && duplicatable" class="col-xs-12">
                    <button :class="grouping.duplicating ? 'disabled' : null" :disabled="grouping.duplicating" @click.prevent="duplicateGrouping" class="btn primary btn-sm pull-right">
                        Duplicate <spinny v-if="grouping.duplicating"></spinny>
                    </button>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
    </div>
</template>
<style>
    .inventory-grouping-image {
        width: 300px;
        height: 300px;
        margin: auto;
    }
</style>
<script>
    import ListableGrouping from '../listables/ListableGrouping.vue';
    import FileUpload from  '../FileUpload.vue';
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            grouping: {
                type: Object,
                required: true
            },
            inventory: {
                type: Array,
            },
            restrictedInventoryIds: {
                type: Array,
            },
            s3_key_url: {
                type: String,
                required: true
            },
            edit: {
                type: Number,
                required: true,
            },
            duplicatable: {
                type: Boolean,
                require: true,
            },
        },
        data() {
            return {
                showingInventory: false,
                deleting: false,
            }
        },
        computed: {
            attachedInventoryId: function () {
                return 'attached-inventory-'+this.grouping.id;
            },
            imageSrc: function () {
                if (this.grouping.image) {
                    return this.grouping.image.location;
                } else {
                    return null;
                }
            },
            toggleInventoryText: function () {
                return (this.showingInventory ? 'Hide' : 'Show') + ' Inventory'
            },
            selectedInventoryIds: function () {
                return this.grouping.inventory.map(function (item) {
                    return item.id;
                });
            },
            maxInitialQty: function () {
                if (this.grouping.inventory.length) {
                    return Array.min(this.grouping.inventory.map((x) => { return x.available_qty; }));
                }
                return null;
            },
        },
        watch: {
            grouping: {
                handler: function (val) {
                    this.limitInitialQtyByMaxInitialQty();
                },
                deep: true
            },
        },
        methods: {
            duplicateGrouping: function () {
                this.grouping.duplicating = true;
                this.$emit('duplicate-grouping');
            },
            destroy: function () {
                this.deleting = true;
                var self = this;
                confirmModal(function () {
                    $.noty.closeAll();
                    self.$emit('delete-grouping');
                }, function () {
                    $.noty.close();
                    self.deleting = false;
                }, {text: 'Are you sure you want to delete this outfit?'});
            },
            limitInitialQtyByMaxInitialQty: function () {
                if (this.maxInitialQty && this.grouping.initial_qty > this.maxInitialQty) {
                    console.log('HOLD ON THERE!');
                }
            },
        },
        created: function () {
            $Bus.$on('image:uploaded:'+this.grouping.id, (el, data)=> {
                this.grouping.image = data;
            });
            $Bus.$on('listable:selected:'+this.grouping.id, (group, subgroup, inventory)=> {
                if (this.selectedInventoryIds.indexOf(inventory.id) == -1) {
                    this.grouping.inventory.push(inventory);
                } else {
                    var index = this.grouping.inventory.indexOf(this.grouping.inventory.find(function (item) {
                        return item.id == inventory.id;
                    }));
                    if (index > -1) {
                        this.grouping.inventory.splice(index, 1);
                        this.grouping.inventory.push(inventory);
                    }
                }
            });
            $Bus.$on('listable:removed:'+this.grouping.id, (group, subgroup, inventory)=> {
                var index = this.grouping.inventory.indexOf(inventory);
                if (index > -1) {
                    this.grouping.inventory.splice(index, 1);
                }
            });
            Array.min = function( array ){
                return Math.min.apply( Math, array );
            };
        },
        components: {
            'inventory-group': ListableGrouping,
            'file-upload': FileUpload,
            'spinny': Spinny,
        }
    }
</script>
