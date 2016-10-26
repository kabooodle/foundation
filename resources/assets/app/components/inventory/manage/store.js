import Vuex from 'vuex';

const state = {
    inventory_route : null,

    // Array of user inventory items
    inventory_items: [],

    // Array of postable objects (flash sales, facebook groups)
    postables : {
        facebookgroups: [],
        flashsales: []
    },

    selected: {
        // Holds postables that have been selected
        postables: {
            fb_albums: [],
            flashsales: []
        },

        // Holds sizes that have been selected
        sizes: [],

        // Holds items that have been selected
        items: [],

        // Toggle of the selected fB group
        fb_group: null,

        // Toggle of the selected fb album
        fb_album: null
    },

    actions : {
        refreshing_data: false,
        posting_to_sales: false,
        fb_advanced_menu: false
    },

    sums : {
        selected_postables: 0,
        selected_sales_sum : 0,
    },
};

const actions = {
    setRoute ({ commit }, route) {
        commit('SET_ROUTE', route);
    }
};

const mutations = {
    RESET_DATA (state, key, value) {
        if(key) {
            let keys = key.split('.');
            return keys.length > 1 ? state[keys[0]][keys[1]] = state(key) : state[key] = state(key);
        }
    },

    SET_ROUTE (state, route) {
        state.inventory_route = route;
    },
    SET_INVENTORY_ITEMS (state, items) {
        state.inventory_items = items;
    },
    SET_POSTABLES (state, postables) {
        state.postables = postables;
    },
    SET_ACTION (state, key, value) {
        state.actions[key] = value;
    },
    SET_SELECTED (state, key, value) {
        state.selected[key] = value;
    },
    SET_SELECTED_ITEMS (state, item) {
        state.selected.items.push(item);
    },
    REMOVE_FROM_SELECTED_ITEMS (state, item){
        state.selected.items.splice(item, 1);
    },
    SET_SELECTED_POSTABLES (state, key, value) {
        state.selected.postables[key] = value;
    },
    SET_SUM (state, key, value) {
        state.sums[key] = value;
    }
};


const getters = {
    getInventoryItems : state => {
        return state.inventory_items;
    },

    // SUMS
    getSums : state => {
        return state.sums;
    },

    // ROUTES
    getInventoryRoute : state => {
        return state.inventory_route;
    },

    // POSTABLES
    getPostables : state => {
        return state.postables;
    },
    getPostable : (state, postable) => {
        return state.postables[postable];
    },

    // SELECTED
    getSelected : state => {
        return state.selected;
    },
    getSelectedPostables : state => {
        return state.selected.postables;
    },
    getSelectedItems : state => {
        return state.selected.items;
    },
    getSelectedSizes : state => {
        return state.selected.sizes;
    },
    getSelectedFbGroup : state => {
        return state.selected.fb_group;
    },
    getSelectedFbAlbum : state => {
        return state.selected.fb_album;
    },

    // ACTIONS
    getActions : state => {
        return state.actions;
    },
    getAction : (state, action) => {
        return state.actions[action];
    }
};

export default new Vuex.Store({
    state,
    mutations,
    getters,
    actions
});