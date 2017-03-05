<template>
    <div>
        <template v-if="!fetched">
            <spinny class="text-center center-block"></spinny>
        </template>
        <template v-else>
            <template v-if=notices.length>
                <notice
                        v-for="notice in notices"
                        :key="notice.id"
                        :id="notice.id"
                        :is_read="notice.is_read"
                        :priority="notice.priority"
                        :title="notice.title"
                        :created_at="notice.created_at"
                        :model="notice"
                        :ref_url="notice.reference_url"
                ></notice>
            </template>
            <template v-else>
                <li class="dropdown-item">
                    You have no notifications.
                </li>
            </template>
        </template>
    </div>
</template>
<script>
    import currentUser from '../current-user';
    import Spinny from '../Spinner.vue';
    import Notice from './Notice.vue';
    export default{
        props : {
            endpoint : {
                required: true,
                type: String
            },
            limit: {
                default: 10
            }
        },
        data: function() {
            return {
                fetched : false,
                notices: [],
                unread: [],
            }
        },
        created : function() {
            let noticeChannel = KABOOODLE_APP.pusher.subscribe('private.'+KABOOODLE_APP.env+'.notices.'+currentUser().id);
            noticeChannel.bind('created', (data)=>{
                this.notices.unshift(data.model);
                this.updateUnreadTotal(this.unread);
            });
            this.getNotices();
        },
        methods : {
            getNotices(){
                $Bus.$emit('fetching:notices', true);
                this.$http.get(this.endpoint).then((response)=> {
                    this.notices = response.body.data.notices.data;
                    this.updateUnreadTotal(response.body.data.unread);
                    this.fetched = true;
                }, (response) => {}).finally(()=>{
                    $Bus.$emit('fetching:notices', false);
                });
            },
            updateUnreadTotal(notices){
                this.unread = notices;
                $Bus.$emit('unread:updated', notices);
            },
        },
        components: {
            'spinny' : Spinny,
            'notice' : Notice
        }
    }
</script>