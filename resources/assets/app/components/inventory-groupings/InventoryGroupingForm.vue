<template>
    <div>
        <div :class="duplicatable ? 'box white' : null">
            <div :class="duplicatable ? 'box-body' : null">
                <div v-if="duplicatable || edit" class="col-xs-12">
                    <span class="pull-right" @click="destroy">
                        <spinny v-if="deleting"></spinny>
                        <i v-else class="fa fa-times text-danger pointer" aria-hidden="true"></i>
                    </span>
                    <div style="clear: both"></div>
                </div>
                <div class="col-sm-8">
                    <div class="identifier-section">
                        <div class="form-group row">
                            <label
                                class="form-control-label col-sm-3"
                                :class="showErrors && validationErrors.name.status ? 'text-danger' : null">Name *</label>
                            <div class="col-sm-6">
                                <input type="text" v-model="grouping.name" class="form-control">
                                <span v-show="showErrors && validationErrors.name.status" class="text-danger">{{ validationErrors.name.message }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="form-control-label col-sm-3">Description</label>
                            <div class="col-sm-6">
                                <textarea v-model="grouping.description" class="form-control" style="resize: none"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="price-section">
                        <div class="form-group row auto-add-section">
                            <div class="col-sm-6 col-sm-offset-3">
                                <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                    <label>
                                        <input
                                            v-model="grouping.auto_add"
                                            data-type="magic-toggler"
                                            type="checkbox" />
                                        <span class="text-sm">Automatically add prices of selected inventory</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                class="form-control-label col-sm-3"
                                :class="showErrors && validationErrors.price_usd.status ? 'text-danger' : null">Price *</label>
                            <div class="col-sm-6">
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0.00"
                                    v-model="grouping.price_usd"
                                    :readonly="grouping.auto_add"
                                    class="form-control">
                                <span v-show="showErrors && validationErrors.price_usd.status" class="text-danger">{{ validationErrors.price_usd.message }}</span>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="form-control-label col-sm-3">Accum Wholesale Price</label>
                            <div class="col-sm-6">
                                <div class="form-control" style="border: none">{{ allInventoryWholesalePriceUsd }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="quantity-section">
                        <div class="form-group row max-quantity-section">
                            <div class="col-sm-6 col-sm-offset-3">
                                <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                    <label>
                                        <input
                                            v-model="grouping.max_quantity"
                                            data-type="magic-toggler"
                                            type="checkbox" />
                                        <span class="text-sm">Maximum quantity based on selected inventory</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                class="form-control-label col-sm-3"
                                :class="(showErrors || exceedsAvailableQty) && validationErrors.initial_qty.status ? 'text-danger' : null"
                            >Quantity *
                            </label>
                            <div class="col-sm-6">
                                <input
                                    type="number"
                                    min="0"
                                    v-model="grouping.initial_qty"
                                    :readonly="grouping.max_quantity"
                                    class="form-control">
                                <span v-show="(showErrors || exceedsAvailableQty) && validationErrors.initial_qty.status" class="text-danger">{{ validationErrors.initial_qty.message }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="inventory-section">
                        <div class="form-group row locked-section">
                            <div class="col-sm-6 col-sm-offset-3">
                                <div class="checkbox m-b-0 checkbox-slider--b-flat">
                                    <label>
                                        <input
                                                v-model="grouping.locked"
                                                data-type="magic-toggler"
                                                type="checkbox" />
                                        <span class="text-sm">{{ lockedMessage }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                class="form-control-label col-sm-3"
                                :class="showErrors && validationErrors.inventory.status ? 'text-danger' : null">
                                <i :class="grouping.locked ? 'fa-lock' : 'fa-unlock'" class="fa text-primary" style="min-width: 16px; margin-right: 5px;"></i>
                                Inventory *
                            </label>
                            <div class="col-sm-6">
                                <div class="box m-b-0 form-control" style="box-shadow: none;">
                                    <div :id=attachedInventoryId class="box-body" style="min-height: 105px;">
                                        <div v-for="(inventory, index) in grouping.inventory"
                                            class="inline m-r-sm">
                                            <span class="avatar_container _48 avatar-thumbnail">
                                                <img :src="inventory.cover_photo" >
                                            </span>
                                            <span class="p-a-o text-sm clearfix block">
                                                <span class="pull-left">Qty:
                                                    <span class="text-muted">{{ inventory.available_qty }}</span>
                                                </span>
                                                <span class="pull-right">
                                                    <i @click="unattachInventory(index)" class="fa fa-times text-danger pointer"></i>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span v-show="showErrors && validationErrors.inventory.status" class="text-danger">{{ validationErrors.inventory.message }}</span>
                                <span class="text-xs text-muted">{{ grouping.inventory.length }} items associated.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="clearfix">
                        <div class="image-section">
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <span class="add-images-btn">
                                        <file-upload
                                            :ukey=grouping.id
                                            :s3_bucket="s3_bucket"
                                            :s3_acl="s3_acl"
                                            :s3_key_url="s3_key_url"
                                            multiple="false"
                                            :button_title="grouping.image ? 'Replace Cover Photo' : 'Add Cover Photo'"
                                        ></file-upload>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-xs-12">
                                    <div v-show="imageSrc" class="item block avatar-thumbnail" id="inventory-grouping-image-div">
                                        <image id="inventory-grouping-image" :src=imageSrc></image>
                                    </div>
                                    <span v-show="showErrors && validationErrors.image.status" class="text-danger">{{ validationErrors.image.message }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 available-inventory-section">
                    <div style="margin: 15px">
                        <span class="text-primary">Available Inventory</span>
                        <small class="text-muted">Qty amounts shown below reflect amounts able to be associated to new outfits.</small>
                    </div>
                    <div v-show="retrievingInventory" class="text-center center-block">
                        <spinny :size="'20'"></spinny>
                    </div>
                    <div v-show="!retrievingInventory">
                        <inventory-group
                            v-for="group in inventory.groups"
                            :key="group.id"
                            :group=group
                            group_type="inventory"
                            :display_footer_buttons="false"
                            :previously-selected-ids=selectedInventoryIds
                            :selected-quantity-adjustment=selectedQuantityAdjustment
                            :use-available-qty=true
                            :disable-unavailable=true
                            v-on:listable-selected=addInventory
                            v-on:listable-removed=removeInventory
                        ></inventory-group>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <label class="form-control-label"><small>* Denotes fields are required.</small></label>
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
    #inventory-grouping-image-div {
        width: 200px;
        height: 200px;
    }
    #inventory-grouping-image {
        border: 1px solid #ccc;
        border-radius: 0.25rem;
        border-color: rgba(120, 130, 140, 0.2);
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
                required: true,
            },
            validationErrors: {
                type: Object,
                required: true,
            },
            inventory: {
                type: Object,
                required: true,
            },
            restrictedInventoryIds: {
                type: Array,
            },
            s3_bucket: {
                type: String,
                required: true
            },
            s3_acl: {
                type: String,
                required: true
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
            },
            showErrors: {
                type: Boolean,
                require: true,
            },
            validating: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                showingInventory: false,
                deleting: false,
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
                return Number(price_usd).toFixed(2);
            },
            allInventoryWholesalePriceUsd: function () {
                var wholesale_price_usd = 0.00;
                this.grouping.inventory.forEach(function (item) {
                    wholesale_price_usd += item.wholesale_price_usd;
                });
                return Number(wholesale_price_usd).toFixed(2);
            },
            quantityError: function () {
                var message = '';
                if (this.exceedsAvailableQty) {
                    message = 'Your outfit quantity can not exceed ' + this.maxInitialQty;
                } else if (this.validationErrors.initial_qty.status) {
                    message = this.validationErrors.initial_qty.message;
                }
                return {
                    'status': this.exceedsAvailableQty || this.validationErrors.initial_qty.status,
                    'message': message,
                };
            },
            selectedQuantityAdjustment: function () {
                if (this.edit && this.grouping.locked) {
                    return this.grouping.initial_qty;
                } else {
                    return 0;
                }
            },
            lockedMessage: function () {
                //if (this.grouping.initial_qty) {
                    //return 'Prevent the quantity of ' + this.grouping.initial_qty + ' from being claimed individually from the attached items';
                //}
                return 'Prevent items from being claimed individually';
            },
        },
        watch: {
            grouping: {
                handler: function (val) {
                    this.limitInitialQtyByMaxInitialQty();
                    if (this.grouping.auto_add) {
                        this.grouping.price_usd = this.allInventoryPriceUsd;
                    }
                    if (this.grouping.max_quantity) {
                        this.grouping.initial_qty = this.maxInitialQty;
                    }
                    this.validateInput();
                },
                deep: true
            },
            validating: function (val) {
                if (val) {
                    this.validateInput();
                }
            },
        },
        methods: {
            addInventory: function (group, subgroup, inventory) {
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
            },
            removeInventory: function (group, subgroup, inventory) {
                var index = this.grouping.inventory.indexOf(inventory);
                if (index > -1) {
                    this.grouping.inventory.splice(index, 1);
                }
            },
            unattachInventory: function (index) {
                var listable = this.grouping.inventory.splice(index, 1)[0];
                $Bus.$emit('listable-unselected', listable);
            },
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
                } else {
                    this.exceedsAvailableQty = false;
                }
            },
            validateInput: function () {
                if (!this.grouping.name || this.grouping.name == '') {
                    this.validationErrors.name.status = true;
                    this.validationErrors.name.message = 'Your outfit must have a name.';
                } else {
                    this.validationErrors.name.status = false;
                    this.validationErrors.name.message = '';
                }

                if (this.grouping.price_usd === '') {
                    this.validationErrors.price_usd.status = true;
                    this.validationErrors.price_usd.message = 'Your outfit must have a price.';
                } else if (this.grouping.price_usd < 0) {
                    this.validationErrors.price_usd.status = true;
                    this.validationErrors.price_usd.message = 'Your outfit must have a positive price.';
                } else {
                    this.validationErrors.price_usd.status = false;
                    this.validationErrors.price_usd.message = '';
                }

                if (this.grouping.initial_qty == '') {
                    this.validationErrors.initial_qty.status = true;
                    this.validationErrors.initial_qty.message = 'Your outfit must have a quantity.';
                } else if (this.grouping.initial_qty < 1) {
                    this.validationErrors.initial_qty.status = true;
                    this.validationErrors.initial_qty.message = 'Your outfit must have a quantity of at least one.';
                } else if (this.exceedsAvailableQty) {
                    this.validationErrors.initial_qty.status = true;
                    this.validationErrors.initial_qty.message = 'Your outfit quantity can not exceed ' + this.maxInitialQty;
                } else {
                    this.validationErrors.initial_qty.status = false;
                    this.validationErrors.initial_qty.message = '';
                }

                if (this.grouping.inventory.length == 0) {
                    this.validationErrors.inventory.status = true;
                    this.validationErrors.inventory.message = 'Your outfit must have inventory attached.';
                } else {
                    this.validationErrors.inventory.status = false;
                    this.validationErrors.inventory.message = '';
                }

                if (!this.grouping.image) {
                    this.validationErrors.image.status = true;
                    this.validationErrors.image.message = 'Your outfit must have an image attached.';
                } else {
                    this.validationErrors.image.status = false;
                    this.validationErrors.image.message = '';
                }

                this.$emit('validated', {'status': this.inputIsValid()});
            },
            inputIsValid: function () {
                return !this.validationErrors.name.status &&
                       !this.validationErrors.price_usd.status &&
                       !this.validationErrors.initial_qty.status &&
                       !this.validationErrors.inventory.status &&
                       !this.validationErrors.image.status;
            },
        },
        created: function () {
            $Bus.$on('image:uploaded:'+this.grouping.id, (el, data)=> {
                this.grouping.image = data;
            });
            Array.min = function (array) {
                return Math.min.apply(Math, array);
            }
        },
        components: {
            'inventory-group': ListableGrouping,
            'file-upload': FileUpload,
            'spinny': Spinny,
        }
    }
</script>
