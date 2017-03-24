<template>
    <span>
        <button
                :disabled="processing || disable || entityIsMe"
                type="button"
                @click="is_following ? unfollowMe($event) : followMe($event)"
                class="btn-follow btn "
                :class="btnclass"
                :key="this.able_type+'_'+this.able_id"
        >
            <span>
                <span class="unfollow" v-if="is_following === true">{{ unfollow_text }}</span>
                <span class="follow" v-else>{{ follow_text }}</span>
            </span>
            <spinner v-if="processing" :size="'' + 10"></spinner>
        </button>
    </span>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import currentUser from '../current-user';
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
            already_following: {
                type: Number,
                default: 0
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
                    return currentUser() || {};
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
                disable: true,
                display: true,
                following: false,
                processing: false,
            }
        },
        created (){
            if (this.doWeHaveCurrentUser()) {
                if (this.already_following == 1) {
                    this.following = true;
                }
            }
        },
        mounted (){
            this.disable = false;
        },
        computed: {
            btnclass : function(){
                let theClass = ' white '+this.btn_size_class;
                if (this.is_following) {
                    theClass = this.btn_active_class + ' ' + this.btn_size_class;
                }
                if (this.processing || this.disable || this.entityIsMe) {
                    theClass = theClass + ' disabled ';
                }

                return theClass;
            },
            is_following: function () {
                if (this.doWeHaveCurrentUser()) {
                    return this.following;
                }
            },
            entityIsMe: function(){
                if (this.doWeHaveCurrentUser()) {
                    return this.able_type.toLowerCase().includes('user') && parseInt(this.current_user.id) == parseInt(this.able_id);
                }
                return false;
            },
        },
        methods: {
            doWeHaveCurrentUser: function () {
                return this.current_user;
            },
            isUserFollowingEntity: function () {
                return this.is_following;
            },
            followMe: function (e) {
                if (currentUser()) {
                    this.processing = true;
                    this.$http.post(this.endpoint).then(()=>{
                        this.following = true;
                    }, function(response){
                        throw new Error(response);
                    }).catch(function() {}).finally(()=>{
                        this.processing = false;
                    });
                } else {
                    notify({
                        'text': 'You must be signed in in order to '+this.follow_text.toLowerCase()+'.',
                        'type': 'information'
                    });
                }
            },
            unfollowMe: function () {
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
        components: {
            'spinner' : Spinny
        }
    }
</script>
