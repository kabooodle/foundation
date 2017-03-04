import Notices from './Notices.vue';

new Vue({
    el: '#notices_wrapper',
    props: {

    },
    data: {
        fetching_new_notices : true,
        previousRequest: null,
        unread: []
    },
    created(){
        $Bus.$on('fetching:notices', (status)=>{
            this.fetching_new_notices = status;
        });
        $Bus.$on('unread:updated', (notices)=>{
            this.updateUnreadTotalsEl(notices);
        });
    },
    methods : {
        updateUnreadTotalsEl(notices){
            this.unread = notices;
            let notifyEl = $('#notify_total');
            notifyEl.removeClass('hide');
            if (notices.length == 0) {
                notifyEl.addClass('hide');
            }
        },
        markUnreadAsRead(endpoint){
            if (this.fetching_new_notices == true || this.unread.length == 0) {
                return false;
            }
            const ids = _.map(this.unread, 'id');
            this.$http.post(endpoint, {ids}, {
                before(request) {
                    if (this.previousRequest) {
                        this.previousRequest.abort();
                    }
                    this.previousRequest = request;
                }
            }).then((response) => {
                this.updateUnreadTotalsEl([]);
            });
        },
    },
    components : {
        'notices' : Notices
    }
});