import Claims_index from './Claims-index.vue';

new Vue({
    el: "#claims_index",
    // methods : {
    //     toggleChecks : (event)=>{
    //         $.each($('input.claim_checks'), function(i,v){
    //             $(this).prop('checked', event.target.checked).trigger('change');
    //         });
    //     },
    //     acceptSelectedClaim: function(event){
    //         event.preventDefault();
    //         const scope = this;
    //         const $el = $(event.target);
    //         const form = $el.closest('form');
    //         this.disableButtons($el);
    //
    //         this.$http.put(form.prop('action'), form.serializeObject()).then(function(response){
    //             notify({text: 'Claim successfully accepted.', type: 'success'});
    //             $Bus.$emit('selected-claim:handled');
    //         }, function(response){
    //             notify({text: response.body.data.msg});
    //         }).finally(function(){
    //             $el.closest('.modal').modal('toggle');
    //             scope.enableButtons($el);
    //         });
    //     },
    //     rejectSelectedClaim: function(event){
    //         event.preventDefault();
    //         const scope = this;
    //         const $el = $(event.target);
    //         const form = $el.closest('form');
    //         this.disableButtons($el);
    //
    //         this.$http.delete(form.prop('action'), form.serializeObject()).then(function(response){
    //             notify({text: 'Claim successfully rejected.', type: 'success'});
    //             $Bus.$emit('selected-claim:handled');
    //         }, function(response){
    //             notify({text: response.body.data.msg});
    //         }).finally(function(){
    //            $el.closest('.modal').modal('toggle');
    //            scope.enableButtons($el);
    //         });
    //     },
    //     enableButtons : function(el){
    //         el.closest('.modal-footer').find('.btn').prop('disabled', false).removeClass('disabled');
    //         el.find('.fa').remove();
    //     },
    //     disableButtons : function(el){
    //         el.closest('.modal-footer').find('.btn').prop('disabled', true).addClass('disabled');
    //         el.html(el.html()+' <i class="fa fa-spin fa-spinner"></i>');
    //     },
    // },
    components: {
        'claims-index' : Claims_index
    }
});