import Vuex from 'vuex';
import initialStates from './state';

const actions = {
    setRoute ({ commit }, route) {
        commit('SET_ROUTE', route);
    }
};

const mutations = {
    CLEAR_READER_STATE (state) {
        state = initialStates;
    },
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
    ADD_TO_SELECTED_ITEMS (state, items) {
        state.selected.items.push(items);
    },
    SET_SELECTED_ITEMS (state, items) {
        state.selected.items = [];
        state.selected.items = items;
    },
    REMOVE_FROM_SELECTED_ITEMS (state, item){
        state.selected.items.splice(item, 1);
    },
    RESET_SELECTED_ITEMS (state) {
        if(state.selected.items.length > 0) {
            state.selected.items = [];
        }
    },
    RESET_SELECTED_FB_GROUP (state) {
        state.selected.fb_group = null;
    },
    RESET_SELECTED_FB_ALBUM (state) {
        state.selected.fb_album = null;
    },
    SET_SELECTED_POSTABLES (state, key, value) {
        state.selected.postables[key] = value;
    },
    RESET_SELECTED_POSTABLES (state){
        state.selected.postables = {
            fb_albums: [],
            flashsales: null
        };
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
    state: initialStates,
    mutations,
    getters,
    actions
});