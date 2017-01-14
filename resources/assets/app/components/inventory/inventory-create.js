import SizeContainers from './create/Size-Containers.vue';

new Vue({
    el: '#inventory',
    data: {
        wholesale_price : '0.00',
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
        /**
         * When the style is changed, the available sizings change also.
         * Tell the world of our new sizings.
         */
        styleChanged : function(e) {
            let styleId = this.getSelectedStyleId();
            this.updateDefaultPricings();
            $Bus.$emit('style-changed', styleId);
        },
        updateDefaultPricings(){
            let styleId = this.getSelectedStyleId();
            let style = _.findWhere(this.inventory_types[0].styles, {id: styleId});

            this.wholesale_price = moneyfy(style.wholesale_price_usd_less_5_percent);
            this.price = moneyfy(style.suggested_price_usd);
        },
    },
    mounted: function(){
        console.log('Inventory ready.');
        this.updateDefaultPricings();
    },
    components: {
        'size-containers' : SizeContainers
    }
});