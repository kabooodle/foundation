import InventorySizing from './inventory-sizing.vue';

new Vue({
    el: '#inventory',
    data: {
        price : '0.00',
        size_containers : [],
        submitting : false
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
                    alert('At least one image must be associated for each size.');
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
        styleChanged : function(e) {
            $Bus.$emit('style-changed', $(e.target).val());
        }
    },
    mounted: function(){
        console.log('Inventory ready.');
    },
    components: {
        'inventory-sizing' : InventorySizing
    }
});