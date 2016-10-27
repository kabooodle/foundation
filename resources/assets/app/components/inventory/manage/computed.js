export default {
    sums : {
        get() {
            return this.$store.getters.getSums;
        }
    },
    data_inventory_route : {
        get() {
            return this.$store.getters.getInventoryRoute;
        },
        set (route) {
            this.$store.commit('SET_ROUTE', 'inventory_route', route);
        },
    },
    inventory_items : {
        get () {
            return this.$store.getters.getInventoryItems;
        },
        set (items) {
            this.$store.commit('SET_INVENTORY_ITEMS', items);
        }
    },
    postables : {
        get () {
            return this.$store.getters.getPostables;
        },
        set (postables) {
            this.$store.commit('SET_POSTABLES', postables);
        }
    },
    actions : {
        get () {
            return this.$store.getters.getActions;
        },
        set (key, value) {
            this.$store.commit('SET_ACTION', key, value);
        }
    },
    selectedItems : {
        get () {
            return this.$store.getters.getSelectedItems;
        }
    },
    selectedPostables : {
        get () {
            return this.$store.getters.getSelectedPostables;
        }
    },
    selected : {
        get () {
            return this.$store.getters.getSelected;
        },
    }
};