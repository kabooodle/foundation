<template>
    <div>
        <div class="col-sm-3">
            <div class="box white">
                <div class="box-body">
                    <div class="col-xs-12">
                        <span class="pull-right" @click="destroy">
                            <spinny v-if="deleting"></spinny>
                            <i v-else class="fa fa-times text-danger pointer" aria-hidden="true"></i>
                        </span>
                    </div>
                    <div class="col-xs-12">
                        <image :src=grouping.coverimage.location style="width: 100%; height: 200px"></image>
                    </div>
                    <div class="col-xs-12">
                        <span class="pull-right" @click="edit">
                            <spinny v-if="editing"></spinny>
                            <i v-else class="fa fa-pencil pointer" aria-hidden="true"></i>
                        </span>
                        {{ grouping.name }}
                    </div>
                    <div style="clear: both"></div>
                </div>
            </div>
        </div>
    </div>
</template>
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
                editRoute: this.inventoryGroupingsIndexRoute + '/' + this.grouping.obfuscate_id + '/edit'
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
