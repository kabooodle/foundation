import InventoryEdit from './edit/inventory-edit.vue';

new Vue({
    el: '#inventory_manage',
    data: {
        images: [],
        item : {}
    },
    created: function(){
        const scope = this;
        $Bus.$on('images:changed', function(images){
            scope.images = images;
        });
    },
    ready: function () {
        console.log('Inventory management ready');
    },
    methods: {
        validateUniqueId : function(){
            return true;
            // return this.$http.post().then(function(response){
            //     return true;
            // }, function(response){
            //     notify({text:  response.body.msg});
            //     return false;
            // });
        },


    },
    components : {
        'inventory-edit' : InventoryEdit
    }
});