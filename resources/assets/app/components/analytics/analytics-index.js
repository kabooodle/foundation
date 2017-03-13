/*
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

require('keen-js');

new Vue({
    el: '#analytics_index',
    data: {
        keenConfig : KCONFIG,
        keenClient: null,
    },
    mounted(){
        this.keenClient = new Keen({
            projectId: this.keenConfig.project_id,
            readKey: this.keenConfig.read_key
        });
    },
    methods: {
        getKeenClient(){
            return this.keenClient;
        },
    }
});