import PhoneNumber from '../phonenumber/PhoneNumber.vue';

new Vue({
    el: '#profile_settings',
    methods : {
        changed : function(event){
            let notification_id = event.target.getAttribute('data-id');
            let type = event.target.getAttribute('data-type');
            let action = event.target.checked ? 'subscribed' : 'unsubscribed';
            let data = {
                'id' : notification_id,
                'action' : action,
                'type' : type
            };
            event.target.classList.add("disabled");
            event.target.disabled=true;
            this.$http.post(notifications_route, data).then(function(response){

            }, function(response){
                notify({text: 'An error occurred, please try again.'});
                event.target.checked = action != 'subscribed';
            }).finally(function(){
                event.target.classList.remove("disabled");
                event.target.disabled=false;
            });
        }
    },
    components: {
        'phone-number': PhoneNumber
    }
});