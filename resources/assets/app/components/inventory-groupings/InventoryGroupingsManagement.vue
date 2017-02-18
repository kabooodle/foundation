<template>
    <div>
        <div class="box white">
            <div class="box-body">
                <div class="pull-right">
                    <button :class="adding ? 'disabled' : null" :disabled="adding" @click.prevent="addGrouping" class="btn primary btn-sm ">
                        Add Outfit <spinny v-if="adding"></spinny>
                    </button>
                    <button :class="saving ? 'disabled' : null" :disabled="saving" @click.prevent="saveGroupings" class="btn primary btn-sm ">
                        {{ saveOutfitsText }} <spinny v-if="saving"></spinny>
                    </button>
                </div>
                <div style="clear: both"></div>
            </div>
        </div>
        <div id="inventory-groupings">
            <inventory-grouping v-for="(grouping, index) in groupings"
                :key="grouping.id"
                :grouping=grouping
                :inventory=inventory
                :restricted-inventory-ids=restrictedInventoryIds
                :s3_key_url="s3_key_url"
                v-on:duplicate-grouping="duplicateGrouping(grouping)"
                v-on:delete-grouping="groupings.splice(index, 1)"
            ></inventory-grouping>
        </div>
    </div>
</template>
<script>
    import InventoryGrouping from './InventoryGrouping.vue'
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            saveInventoryGroupingsEndpoint: {
                type: String,
                required: true,
            },
            getInventoryEndpoint: {
                type: String,
                required: true,
            },
            s3_key_url: {
                type: String,
                required: true
            },
        },
        data() {
            return {
                adding: false,
                saving: false,
                ids: [],
                groupings: [],
                inventory: [],
                restrictedInventoryIds: [],
            }
        },
        created: function () {
            this.addGrouping();
            this.getInventory();
        },
        computed: {
            saveGroupingsData: function () {
                return {
                    'groupings': this.groupings,
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
                this.$http.get(this.getInventoryEndpoint)
                    .then(function (response) {
                        self.inventory = response.data.data;
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
            saveGroupings: function () {
                var self = this;
                self.saving = true;
                this.$http.post(this.saveInventoryGroupingsEndpoint, this.saveGroupingsData)
                    .then(function (response) {
                        notify({
                            'text': 'Your outfit' + (this.groupings.length > 1 ? 's were' : ' was') +' saved!',
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
            }
        },
        components: {
            'inventory-grouping': InventoryGrouping,
            'spinny': Spinny,
        },
    }
</script>
