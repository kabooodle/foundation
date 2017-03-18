import SizeContainers from './create/Size-Containers.vue';
import onboard from './create/onboard';
import Modal from '../Modal.vue';

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
        /**
         *
         * @returns {boolean}
         */
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
        /**
         *
         * @param val
         */
        setSubmitting : function(val) {
            this.submitting = val;
        },
        /**
         *
         * @param e
         * @returns {boolean}
         */
        validateForm: function (e) {
            if(! this.validateSizeContainers()){
                e.preventDefault();
                this.setSubmitting(false);
                return false;
            }

            this.setSubmitting(true);
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
            let style = _.find(this.inventory_types[0].styles, {id: styleId});

            this.wholesale_price = Number(style.wholesale_price_usd_less_5_percent).toFixed(2);
            this.price = Number(style.suggested_price_usd).toFixed(2);
        },
        showTourModal(){
            $('#tour-modal').modal('show');
        },
        startTour(){
            $(document).find('.tour-modal').modal('hide');
            $(document).find('.onboard-show-btn').hide();
            onboard().start();
        },
        noTour(){
            $(document).find('.tour-modal').modal('hide');
        },
    },
    mounted: function(){
        console.log('Inventory ready.');
        this.updateDefaultPricings();
        this.addSizeContainer();
    },
    components: {
        'modal' : Modal,
        'size-containers' : SizeContainers
    }
});