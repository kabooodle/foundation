import fileupload from '../FileUpload.vue';

new Vue({
    el: '#profile_settings',
    created : ()=>{
        $Bus.$on('image:uploaded', function(el, data){
            console.log(data);
        });
    },
    components: {
        'file-upload' : fileupload
    }
});