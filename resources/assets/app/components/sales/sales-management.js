
new Vue({
    el: '#sales_index',
    mounted : function(){
      $(document).on('click', '[data-toggle="toggleShippingMethod"]', this.toggleShippingMethod);
    },
    methods: {
        toggleShippingMethod:function(event){
            let $that = $(event.target);
            let url = event.target.dataset.route;
            let method = event.target.dataset.method;

            $that.html($that.html() + spinny());

            $that.closest('tr')
                .addClass('disabled text-muted')
                .prop('disabled', true)
                .find('button, input, .btn')
                .addClass('disabled')
                .prop('disabled', true);

            this.$http.post(url, {method: method}).then(function(response){
                $that.closest('tr').html(response.body.data)
                    .removeClass('disabled text-muted')
                    .prop('disabled', false)
                    .find('button, input, .btn')
                    .removeClass('disabled')
                    .prop('disabled', false);
            }, function(response){

            }).then(function(){
                $that.closest('tr')
                    .removeClass('disabled text-muted')
                    .prop('disabled', false)
                    .find('button, input, .btn')
                    .removeClass('disabled')
                    .prop('disabled', false);
            });
        }
    }
});