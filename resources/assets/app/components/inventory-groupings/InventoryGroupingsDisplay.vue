<template>
    <div>
        <div v-show="retrievingInventoryGroupings" class="text-center center-block">
            <spinny :size="'20'"></spinny>
        </div>
        <div v-show="!retrievingInventoryGroupings">
            <inventory-grouping v-for="(grouping, index) in groupings"
                :key=grouping.id
                :grouping=grouping
                v-on:delete-grouping="deleteGrouping(grouping, index)"
            ></inventory-grouping>
        </div>
    </div>
</template>
<script>
    import InventoryGroupingShow from './InventoryGroupingShow.vue';
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            inventoryGroupingsEndpoint: {
                type: String,
                required: true,
            },
        },
        data() {
            return {
                retrievingInventoryGroupings: false,
                groupings: [],
            }
        },
        created: function () {
            this.getInventoryGroupings();
        },
        methods: {
            getInventoryGroupings: function () {
                var self = this;
                self.retrievingInventoryGroupings = true;
                this.$http.get(this.inventoryGroupingsEndpoint)
                    .then(function (response) {
                        self.groupings = response.data.data.groupings;
                    }, function (response) {

                    }).finally(()=>{
                        self.retrievingInventoryGroupings = false;
                });
            },
            deleteGrouping: function (grouping, index) {
                var self = this;
                confirmModal(function () {
                    $.noty.closeAll();
                    this.$http.delete(this.inventoryGroupingsEndpoint+'/'+grouping.id)
                        .then(function (response) {
                            self.groupings.splice(index, 1);
                            notify({
                                'text': 'Your outfit has been deleted!',
                                'type': 'success'
                            });
                        }, function (response) {
                            notify({
                                'text': 'There was a problem deleting your outfit. Please try again.',
                                'type': 'error'
                            });
                        }).finally(()=>{
                            self.retrievingInventoryGroupings = false;
                    });
                }, function () {
                    $.noty.close();
                }, {text: 'Are you sure you want to delete this outfit?'});
            },
        },
        components: {
            'inventory-grouping': InventoryGroupingShow,
            'spinny': Spinny,
        }
    }
</script>
