import InventoryEdit from './edit/inventory-edit.vue';

new Vue({
    el: '#inventory_manage',
    ready: function () {
        console.log('Inventory management ready');
    },
    methods: {
        validateForm: function (e) {
            if (this.images.length == 0) {
                e.preventDefault();
                alert('Must have at least 1 image');
                return false;
            }

//                this.$validate(true, function () {
//                    if (scope.$inventory_validation.invalid || scope.images.length == 0) {
//                        e.preventDefault();
//                        if (scope.images.length == 0) {
//                            alert('Must have at least 1 image');
//                        }
//                        return false;
//                    }
//                });
        },

    },
    components : {
        'inventory-edit' : InventoryEdit
    }
});