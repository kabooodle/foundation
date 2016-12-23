
new Vue({
    el: '#profile_settings',
    methods : {
        subscribeToTrial : function(event){
            let target = event.target;
            let innerHtml = target.innerHTML;
            target.classList.add('disabled');
            target.disabled = true;
            target.innerHTML = target.innerHTML + (spinny());

            this.$http.post(sub_endpoint).then(function(response){
                target.innerHTML = 'Success! One moment... ' + (spinny());
                notify({text: response.body.data.msg, type: 'success'});
                setTimeout(function(){
                    window.location.href = response.body.data.redirect;
                }, 2500);
            }, function(response){
                let msg = 'An error occurred. Please try again.';
                if (response.body.data.msg) {
                  msg = response.body.data.msg;
                }
                notify({text: msg});
                target.classList.remove('disabled');
                target.disabled = false;
                target.innerHTML = innerHtml;
            });
        }
    }
});