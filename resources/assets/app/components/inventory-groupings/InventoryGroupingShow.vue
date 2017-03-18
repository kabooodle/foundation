<template>
    <div>
        <div class="col-sm-3 m-b-md">
            <div class="box white m-b-sm">
                <div class="box-body">
                    <div class="col-xs-12">
                        <span class="pull-right" @click="destroy">
                            <spinny v-if="deleting"></spinny>
                            <i v-else class="fa fa-times text-danger pointer" aria-hidden="true"></i>
                        </span>
                        <h6>
                            {{ grouping.name }}
                        </h6>
                        <div class="avatar_container inline avatar-thumbnail">
                            <image :src=grouping.coverimage.location></image>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <span class="text-sm clearfix block" style="margin: 5px">
                            <span class="pull-left">
                                Qty:
                                <span class="text-muted">{{ grouping.available_quantity }}</span>
                            </span>
                            <span class="text-muted pull-right">${{ Number(grouping.price_usd).toFixed(2) }}</span>
                        </span>
                    </div>
                    <div style="clear: both"></div>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="text-center center-block">
                    <button type="button" class="btn btn-xs _400 white"><a :href=viewRoute>View</a></button>
                    <button type="button" class="btn btn-xs _400 white"><a :href=editRoute>Edit</a></button>
                    <button type="button" class="btn btn-xs _400 white"><a :href=claimRoute>Claim</a></button>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .delete-outfit-icon {
        position: absolute;
        top: -13px;
        right: -6px;
    }
</style>
<script>
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            grouping: {
                type: Object,
                required: true,
            },
            inventoryGroupingsIndexRoute: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                editing: false,
                deleting: false,
                viewRoute: this.inventoryGroupingsIndexRoute + '/' + this.grouping.obfuscate_id,
                editRoute: this.inventoryGroupingsIndexRoute + '/' + this.grouping.obfuscate_id + '/edit',
                claimRoute: this.inventoryGroupingsIndexRoute + '/' + this.grouping.obfuscate_id,
            }
        },
        methods: {
            edit: function () {
                this.editing = true;
                window.location.href = this.editRoute;
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
                }, {text: 'Are you sure you want to permanently delete this outfit?'});
            },
        },
        components: {
            'spinny': Spinny,
        }
    }
</script>
