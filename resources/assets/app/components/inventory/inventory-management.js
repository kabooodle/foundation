/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import FacebookLogin from '../facebook/FacebookLogin.vue';
import ListableGroupings from '../listables/ListableGroupings.vue';
import OnboardCard from '../OnboardCard.vue';
import Spinner from '../Spinner.vue';

new Vue({
    el:'#manage_inventory',
    data:{
        actions: {
            refreshing_data: true,
        },
        listables: [],
        selected : {
            listables: []
        }
    },
    created(){
        $Bus.$on('listables:fetched', (listables)=>{
            this.listables = listables;
            this.actions.refreshing_data = false;
        });
        $Bus.$on('listings:selected:listable:added', (group, subgroup, listable)=>{
            const index = _.findIndex(this.selected.listables, {id: listable.id});
            if (index == -1) {
                this.selected.listables.push(listable);
            }
        });
        $Bus.$on('listings:selected:listable:removed', (groupid, subgroup, listable)=>{
            const index = _.findIndex(this.selected.listables, {id: listable.id});
            if (index > -1) {
                this.selected.listables.splice(index, 1);
            }
            // this.selected.listables = new Function('return ' + this.selected.listables + incr)();
        });
    },
    methods: {
        resetSelectedListables(){
            this.selected.listables = [];
            $Bus.$emit('listings:selected:listables:reset');
        },
        selectAllListables(){
            $Bus.$emit('listings:listables:select:all');
        },
    },
    components:{
        'listable-groupings' : ListableGroupings,
        'facebook-login' : FacebookLogin,
        'onboard-card' : OnboardCard,
        'spinner': Spinner,
    }
});