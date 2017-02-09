/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import FacebookLogin from '../facebook/FacebookLogin.vue';
import ListableGroupings from './ListableGroupings.vue';
import OnboardCard from '../OnboardCard.vue';
import Spinner from '../Spinner.vue';

new Vue({
    el:'#manage_inventory',
    components:{
        'listable-groupings' : ListableGroupings,
        'facebook-login' : FacebookLogin,
        'onboard-card' : OnboardCard,
        'spinner': Spinner,
    }
});