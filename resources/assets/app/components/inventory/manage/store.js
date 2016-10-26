import Vuex from 'vuex';

Vue.use(Vuex);

const state = {
    selected_sales : {
        flashsales : [],
        facebook : []
    },
    data_items : [],
    data_items_endpoint : null,
    date_time_input : null,
    selected_items: [],
    refreshing_data: false,
    posting_to_sales: false,
};

const mutations = {
    set_data_items_endpoint : function(state, endpoint){
        state.data_items_endpoint = endpoint;
    }
};


export default {
    state,
    mutations
};