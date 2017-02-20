<template>
    <div>
        <div class="duplicatable ? 'box white' : null">
            <div :class="duplicatable ? 'box-body' : null">
                <div class="col-xs-12">
                    <span class="pull-right" @click="destroy">
                        <spinny v-if="deleting"></spinny>
                        <i v-else class="fa fa-times text-danger pointer" aria-hidden="true"></i>
                    </span>
                    <div style="clear: both"></div>
                </div>
                <div class="col-sm-8">
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Name</label>
                        <div class="col-sm-6">
                            <input type="text" v-model="grouping.name" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Description</label>
                        <div class="col-sm-6">
                            <textarea v-model="grouping.description" class="form-control" style="resize: none"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="form-control-label">Price</label>
                            <input type="checkbox" v-model="autoAddPriceUsd">
                            <label>Auto Add</label>
                        </div>
                        <div class="col-sm-6">
                            <input
                                type="number"
                                step="0.01"
                                min="0.00"
                                v-model="grouping.price_usd"
                                :readonly="autoAddPriceUsd"
                                class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="form-control-label col-sm-3">Wholesale Price</label>
                        <div class="col-sm-6">
                            <div class="form-control" style="border: none">{{ allInventoryWholesalePriceUsd }}</div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="form-control-label" :class="exceedsAvailableQty ? 'text-danger' : null">Quantity</label>
                            <input type="checkbox" v-model="forceMaxQuantity">
                            <label>Max</label>
                        </div>
                        <div class="col-sm-6">
                            <input
                                type="number"
                                min="0"
                                v-model="grouping.initial_qty"
                                :readonly="forceMaxQuantity"
                                class="form-control">
                            <span v-if="exceedsAvailableQty" class="text-danger">The quantity of this outfit can not exceed {{ maxInitialQty }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="form-control-label">Inventory</label>
                            <span @click="grouping.locked = !grouping.locked">
                            <i :class="grouping.locked ? 'fa-lock' : 'fa-unlock'" class="fa text-primary pointer"></i>
                            <div>
                                <span class="text-xs text-muted m-l-2">{{ grouping.inventory.length }} items associated.</span>
                            </div>
                        </span>
                        </div>
                        <div class="col-sm-6">
                            <div class="box">
                                <div :id=attachedInventoryId class="box-body">
                                    <span v-for="(inventory, index) in grouping.inventory"
                                          class="avatar_container m-r-sm inline _48 avatar-thumbnail" >
                                        <img :src="inventory.cover_photo.location">
                                        <i @click="grouping.inventory.splice(index, 1)" class="fa fa-times fa-2x text-danger pointer"></i>
                                    </span>
                                </div>
                            </div>
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
                <div class="col-sm-12">
                    <div style="margin: 15px">
                        <span class="text-primary">Available Inventory</span>
                        <small class="text-muted">Qty amounts shown below reflect amounts able to be associated to new outfits.</small>
                    </div>
                    <div v-show="retrievingInventory" class="text-center center-block">
                        <spinny :size="'20'"></spinny>
                    </div>
                    <div v-show="!retrievingInventory">
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
            retrievingInventory: {
                type: Boolean,
                require: true,
            }
        },
        data() {
            return {
                showingInventory: false,
                deleting: false,
                autoAddPriceUsd: true,
                forceMaxQuantity: true,
                exceedsAvailableQty: false,
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
                return (this.showingInventory ? 'Hide' : 'Show') + ' Available Inventory'
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
            allInventoryPriceUsd: function () {
                var price_usd = 0.00;
                this.grouping.inventory.forEach(function (item) {
                    price_usd += item.price_usd;
                });
                return price_usd;
            },
            allInventoryWholesalePriceUsd: function () {
                var wholesale_price_usd = 0.00;
                this.grouping.inventory.forEach(function (item) {
                    wholesale_price_usd += item.wholesale_price_usd;
                });
                return wholesale_price_usd;
            },
        },
        watch: {
            grouping: {
                handler: function (val) {
                    this.limitInitialQtyByMaxInitialQty();
                    if (this.autoAddPriceUsd) {
                        this.grouping.price_usd = this.allInventoryPriceUsd;
                    }
                    if (this.forceMaxQuantity) {
                        this.grouping.initial_qty = this.maxInitialQty;
                    }
                },
                deep: true
            },
            autoAddPriceUsd: function (val) {
                if (val === true) {
                    this.grouping.price_usd = this.allInventoryPriceUsd;
                }
            },
            forceMaxQuantity: function (val) {
                if (val === true) {
                    this.grouping.initial_qty = this.maxInitialQty;
                }
            }
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
                    this.exceedsAvailableQty = true;
                    console.log('HOLD ON THERE!');
                } else {
                    this.exceedsAvailableQty = false;
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
