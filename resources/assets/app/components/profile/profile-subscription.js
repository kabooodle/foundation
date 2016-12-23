
new Vue({
    el: '#profile_settings',
    methods : {
        subscribeToTrial : function(event){
            let target = event.target;
            target.classList.add('disabled');
            target.disabled = true;

            this.$http.post(sub_endpoint).then(function(response){
                notify({text: 'An error occurred, please try again.', type: 'success'});
            }, function(response){
                notify({text: 'An error occurred, please try again.'});
            }).finally(function(){
                target.classList.remove('disabled');
                target.disabled = false;
            });
        }
    }
});