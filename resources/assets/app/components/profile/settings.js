import fileupload from '../FileUpload.vue';

new Vue({
    el: '#profile_settings',
    data : {
        avatar: null
    },
    created : function(){
        const scope = this;
        $Bus.$on('image:uploaded', (el, data)=>{
            $('.avatar_container').html('<img src="'+data.location+'"><input type="hidden" name="avatar" value="'+data.location+'">');
            scope.avatar = data.location;
        });
    },
    components: {
        'file-upload' : fileupload
    }
});