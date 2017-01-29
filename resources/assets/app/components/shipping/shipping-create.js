import Modal from '../Modal.vue';
import Spinny from '../Spinner.vue';
import InlineField from '../InlineField.vue';
import ShippingParcelForm from './ShippingParcelForm.vue';

new Vue({
    el: '#shipping_create',
    data : {
        completed_steps: {
            parcel: false,
            pricing: false,
        },
        rates: [],
        shipment: {}
    },
    created(){
        $Bus.$on('parcel:saved', (parcel, rates, shipment)=>{
            this.completed_steps.parcel = true;
            this.rates = rates;
            this.shipment = shipment;
        });
    },
    methods: {
        viewParcelData(){
            this.completed_steps.parcel = false;
            this.rates = [];
            this.shipment = {};
        },
        purchaseLabel(url, uuid, event){
            confirmModal(()=>{
                $('.noty-btn').addClass('disabled').prop('disabled', true);
                $('.noty-btn-primary').html(spinny());

                const form_data = {
                    rate_uuid : uuid,
                    parcel_id : parseInt(this.shipment.id)
                };

                this.$http.post(url, form_data).then((response)=>{
                    notify({type: 'success', text : response.body.data.msg});
                    setTimeout(function(){
                        window.location.href = response.body.data.redirect;
                    }, 2000);
                }, (response)=>{
                    $.noty.closeAll();
                    notify({'text' : response.body.data.msg});
                });
            }, ($noty)=>{
                $noty.close();
            });
        },
    },
    components: {
        'modal' : Modal,
        'spinny' : Spinny,
        'inline-field' : InlineField,
        'shipping-parcel-form' : ShippingParcelForm,
    }
});