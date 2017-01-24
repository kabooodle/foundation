import fileupload from '../FileUpload.vue';

new Vue({
    el: '#profile_settings',
    data : {
        avatar: null
    },
    created : function(){
        const scope = this;
        $Bus.$on('image:uploaded', (el, data)=>{
            let json = (data.json).replace(/'/g, "\\'");
            $('.avatar_container').html('' +
                '<img src="'+data.location+'"><input type="hidden" name="avatar" value=\''+json+'\'>');
            scope.avatar = data.location;
        });
    },
    methods : {
        removeAvatar: function(event){
            event.preventDefault();
            this.avatar = null;
            $('.avatar_container').html('');
        }
    },
    components: {
        'file-upload' : fileupload
    }
});