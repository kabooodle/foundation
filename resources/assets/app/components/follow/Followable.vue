<template>
    <span>
        <button
                :disabled="processing || disable"
                type="button"
                @click="is_following ? unfollowMe($event) : followMe($event)"
                class="btn-follow btn "
                :class="btnclass"
        >
            <span v-html="is_following ? unfollow_text : follow_text"></span>
            <spinner v-if="processing" :size="'' + 8"></spinner>
        </button>
    </span>
</template>
<script>
    import Spinny from '../Spinner.vue';
    import currentUser from '../current-user';
    export default{
        props: {
            show_on_hover: {
                default: 0
            },
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
                    return (currentUser() ? currentUser() : {});
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
                if (this.isUserFollowingEntity()) {
                    this.following = true;
                }
            }
        },
        mounted (){
            this.disable = false;
//            if (! this.entityIsMe()) {
//                this.disable = false;
//            }
        },
        computed: {
            btnclass : function(){
                let theClass = ' white '+this.btn_size_class;
                if (this.following) {
                    theClass = this.btn_active_class + ' ' + this.btn_size_class;
                }
                if (this.processing || this.disable) {
                    theClass = theClass + ' disabled ';
                }

                if (this.show_btn_on_hover) {
                    if (! this.following && ! this.processing) {
                        theClass = theClass + ' show-on-overlay-hover ';
                    }
                }

                return theClass;
            },
            is_following(){
                if (this.doWeHaveCurrentUser()) {
                    return (this.already_following === true || this.already_following === 'true' || this.following === 'true' || this.following === true);
                }
            },
            show_btn_on_hover(){
                return this.show_on_hover === 1 || this.show_on_hover === '1';
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
                        'text': 'You must be signed in to '+this.follow_text.toLowerCase(),
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
