import fileupload from '../FileUpload.vue';

new Vue({
    el: '#profile_settings',
    created : ()=>{
        $Bus.$on('image:uploaded', (el, data)=>{
            $('#profile_photo_container').html('<img src="'+data.location+'">');
        });
    },
    components: {
        'file-upload' : fileupload
    }
});