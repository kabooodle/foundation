/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

import FacebookLogin from '../facebook/FacebookLogin.vue';
import CreateListing from './create/CreateListing.vue';
import OnboardCard from '../OnboardCard.vue';
import Spinner from '../Spinner.vue';

new Vue({
    el:'#create_listings',
    data:{},
    components:{
        'create-listing' : CreateListing,
        'facebook-login' : FacebookLogin,
        'onboard-card' : OnboardCard,
        'spinner': Spinner,
    }
});