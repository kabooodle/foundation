<template>
    <span v-if="display">
        <button
                :disabled="processing"
                type="button"
                @click="following ? unfollowMe($event) : followMe($event)"
                class="btn-follow btn "
                :class="btnclass"
        >
            {{ following ? unfollow_text : follow_text }} <spinny v-if="processing"></spinny>
        </button>
    </span>
</template>
<script>
    export default{
        props: {
            able_name : {
                type : String,
                default: function(){
                    return 'followable';
                }
            },
            able_id: {
                required: true,
                type: String
            },
            able_type: {
                required: true,
                type: String
            },
            btn_active_class : {
                type: String,
                default: function(){
                    return ' primary ';
                }
            },
            btn_size_class : {
                type: String,
                default: function(){
                    return ' btn-xs ';
                }
            },
            endpoint : {
                type: String,
                required: true
            },
            current_user: {
                type: Object,
                default: function () {
                    return ((KABOOODLE_APP && KABOOODLE_APP.currentUser) ? KABOOODLE_APP.currentUser : {})
                }
            },
            unfollow_text: {
                type: String,
                default: 'Following'
            },
            follow_text: {
                type: String,
                default: 'Follow'
            }
        },
        data (){
            return {
                already_following: false,
                display: false,
                following: false,
                processing: false,
            }
        },
        created (){
            if (this.doWeHaveCurrentUser()) {
                if (this.isUserFollowingEntity()) {
                    this.following = true;
                }
            }
        },
        mounted (){
            if (! this.entityIsMe()) {
                this.display = true;
            }
        },
        computed: {
            btnclass : function(){
                let theClass = ' white '+this.btn_size_class;
                if (this.following) {
                    theClass = this.btn_active_class + ' ' + this.btn_size_class;
                }
                if (this.processing) {
                    theClass = theClass + ' disabled ';
                }

                return theClass;
            }
        },
        methods: {
            entityIsMe: function(){
                if(this.doWeHaveCurrentUser()) {
                    return parseInt(this.current_user.id) == parseInt(this.able_id);
                }

                return false;
            },
            doWeHaveCurrentUser: function () {
                return this.current_user;
            },
            isUserFollowingEntity: function () {
                if (this.doWeHaveCurrentUser()) {
//                    const typeName = this.able_name+'_type';
//                    const typeId = this.able_name+'_id';
//                    let attrs = {
//                        user_id: parseInt(this.current_user.id)
//                    };
//                    attrs[typeName] =  this.able_type;
//                    attrs[typeId] = parseInt(this.able_id);

                    return (this.already_following == '1');
                }

                return false;
            },
            followMe: function (e) {
                if (KABOOODLE_APP.currentUser) {
                    this.processing = true;
                    this.$http.post(this.endpoint).then(()=>{
                        this.following = true;
                        this.already_following = true;
                    }, function(response){
                        throw new Error(response);
                    }).catch(function() {}).finally(()=>{
                        this.processing = false;
                    });
                } else {
                    notify({
                        'text': 'You must be signed in to '+this.follow_text.toLowerCase(),
                        'type': 'information'
                    });
                }
            },
            unfollowMe: function () {
                this.processing = true;
                this.$http.delete(this.endpoint).then(()=>{
                    this.following = false;
                    this.already_following = false;
                }, function(response){
                    throw new Error(response);
                }).catch(function() {}).finally(()=>{
                    this.processing = false;
                });
            }
        },
    }
</script>
