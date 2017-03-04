<template>
    <span v-if="display">
        <button
                :disabled="processing"
                type="button"
                @click="following ? unfollowMe() : followMe()"
                class="btn-follow btn "
                :class="btnclass"
        >
            {{ following ? unfollow_text : follow_text }} <spinny v-if="processing"></spinny>
        </button>
    </span>
</template>
<script>
    import currentUser from '../current-user';
    export default{
        props: {
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
                    return currentUser() || {};
                }
            },
            current_user_following: {
                type: Array,
                default: function () {
                    return (currentUser() ? currentUser().following : {})
                }
            },
            followable_id: {
                required: true,
                type: String
            },
            followable_type: {
                required: true,
                type: String
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
            if (this.doWeHaveCurrentUser() && ! this.entityIsMe()) {
                this.display = true;
            }
        },
        computed : {
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
            entityIsMe : function(){
                if(this.doWeHaveCurrentUser()) {
                    return parseInt(this.current_user.id) == parseInt(this.followable_id);
                }

                return false;
            },
            doWeHaveCurrentUser(){
                return this.current_user;
            },
            isUserFollowingEntity(){
                if (this.doWeHaveCurrentUser()) {
                    return _.find(this.current_user_following, {
                        followable_type: this.followable_type,
                        followable_id: parseInt(this.followable_id),
                        user_id: parseInt(this.current_user.id)
                    });
                }

                return false;
            },
            followMe(){
                this.processing = true;
                this.$http.post(this.endpoint).then(()=>{
                    this.following = true;
                }, function(response){
                    throw new Error(response);
                }).catch(function() {}).finally(()=>{
                    this.processing = false;
                });
            },
            unfollowMe(){
                this.processing = true;
                this.$http.delete(this.endpoint).then(()=>{
                    this.following = false;
                }, function(response){
                    throw new Error(response);
                }).catch(function() {}).finally(()=>{
                    this.processing = false;
                });
            }
        },
    }
</script>
