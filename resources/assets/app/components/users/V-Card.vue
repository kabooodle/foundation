<template>
    <div class="box b b-a m-b-0 no-shadow white">
        <div class="list-item p-a-sm p-sm" :class="extra_class" >
            <div class="list-left" v-if="use_avatar">
                <span class="avatar_container inline avatar-thumbnail" :class="'_'+avatar_size">
                    <img alt="..." :src="user.avatar ? user.avatar : '/assets/images/logo/roboto-avatar.png'">
                </span>
            </div>
            <div class="list-body clearfix">
                <small class="block _500 text-ellipsis m-b-sm">{{ user.username }}</small>
                <div class="btn-group">
                    <followable
                            :already_following="already_following"
                            :endpoint="follow_endpoint"
                            :able_type="able_type"
                            :able_id="able_id"
                    ></followable>
                    <message-user
                            :recipient_id="user.id"
                            :recipient_name="user.username"
                            :endpoint="message_endpoint"
                    ></message-user>
                </div>
            </div>
        </div>
        <!--<div class="row no-gutter b-t">-->
            <!--<div class="col-xs-6 b-r">-->
                <!--<span class="p-sm block text-center" ui-toggle-class="">-->
                    <!--<strong class="block">30</strong>-->
                    <!--<span class="block text-xs text-muted">Followers</span>-->
                <!--</span>-->
            <!--</div>-->
            <!--<div class="col-xs-6">-->
                <!--<span class="p-sm block text-center" ui-toggle-class="">-->
                    <!--<strong class="block">30</strong>-->
                    <!--<span class="block text-xs text-muted">Following</span>-->
                <!--</span>-->
            <!--</div>-->
        <!--</div>-->
    </div>
</template>
<script>
    import currentUser from '../current-user';
    import MessageUser from '../messenger/MessageUser.vue';
    import Followable from '../follow/Followable.vue';
    export default{
        props: {
            avatar_size: {
                default: '64'
            },
            extra_class: {},
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
            user: {
                required: true,
                type: Object
            },
            message_endpoint: {
                required: true,
                type: String,
            },
            follow_endpoint: {
                required: true,
                type: String
            },
            include_avatar: {
                default: true
            }
        },
        data(){
            return{
                current_user: currentUser() ? currentUser() : {}
            }
        },
        computed: {
            use_avatar(){
                return (this.include_avatar === 'true' || this.include_avatar === true);
            }
        },
        components:{
            'followable' : Followable,
            'message-user' : MessageUser,
        }
    }
</script>
