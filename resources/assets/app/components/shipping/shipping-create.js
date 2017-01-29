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
        purchaseLabel(){

        },
    },
    components: {
        'modal' : Modal,
        'spinny' : Spinny,
        'inline-field' : InlineField,
        'shipping-parcel-form' : ShippingParcelForm,
    }
});