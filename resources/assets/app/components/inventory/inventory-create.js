import InventorySizing from './inventory-sizing.vue';

new Vue({
    el: '#inventory',
    data: {
        price : '0.00',
        size_containers : [],
        submitting : false,
        inventory_types : KABOOODLE_APP.inventory_types
    },
    methods : {
        validateSizeContainers : function() {
            var containers = this.size_containers;
            var valid = true;
            $.each(containers, function(i,container){
                var $containerEl = $('#size_'+container.id),
                    $validationEls = $containerEl.find('[validation]');
                if (! $validationEls.is(':checked')) {
                    $validationEls.closest('.form-group').addClass('has-danger');
                    valid = false;
                }

                if (container.images.length == 0) {
                    valid = false;
                    notify({text:  'At least one image must be associated for each size.'});
                    return valid;
                }
            });

            return valid;
        },
        setSubmitting : function(val) {
            this.submitting = val;
        },
        validateForm: function (e) {
            if(! this.validateSizeContainers()){
                e.preventDefault();
                this.setSubmitting(false);
                return false;
            }

            this.setSubmitting(true);
            // this.$validate(true, function () {
            //     if (self.$inventory_validation.invalid || ! self.validateSizeContainers()) {
            //         e.preventDefault();
            //         self.setSubmitting(false);
            //         return false;
            //     }
            // })
        },
        addSizeContainer : function() {
            $Bus.$emit('add-size');
        },
        getSelectedStyleId(){
            return parseInt($('#inventory-styles-el').val());
        },
        styleChanged : function(e) {
            let styleId = this.getSelectedStyleId();
            this.updateWholesalePrice();
            $Bus.$emit('style-changed', styleId);
        },
        updateWholesalePrice(){
            let styleId = this.getSelectedStyleId();
            let style = _.findWhere(this.inventory_types[0].styles, {id: styleId});
            let ws_price_5 = moneyfy(style.wholesale_price_usd_less_5_percent);
            let ws_price = moneyfy(style.wholesale_price_usd);
            $('#inventory-wholesale-el')
                .val(ws_price)
                .prop('placeholder', ws_price);
        },
    },
    mounted: function(){
        console.log('Inventory ready.');
        this.updateWholesalePrice();
    },
    components: {
        'inventory-sizing' : InventorySizing
    }
});