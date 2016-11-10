import vuetable from './shipping-transactions.vue';

new Vue({
    el: '#shipping_index',
    data: {
        ready : false,
        moreParams: [],
        selectedItems: [],
        columns: [
            {
                name: 'files',
                callback: 'setPropItem'
            },
            {
                name: 'style',
                callback: 'setPropStyle'
            },
            {
                name: 'style_size',
                title: 'size',
                callback: 'setPropSize'
            },
            {
                name: 'price_usd',
                title: 'Price',
                callback: 'setPropPrice'
            },
            {
                name: 'initial_qty',
                title: 'Qty'
            },
            {
                name: 'pending_claims',
                title: 'claims',
                callback: 'setPropClaims'
            },
            {
                name: 'sales',
                callback: 'setPropSales'
            },
        ]
    },
    methods: {
        setPropSales: function (v) {
            return v.length;
        },
        setPropClaims: function (v) {
            return v.length;
        },
        setPropItem: function (v) {
            var firstImg = v ? v[0] : null;
            return firstImg ? '<img style="width: 32px; height: 32px; border-radius: 2px;" src="' + firstImg.location + '">' : null;
        },
        setPropSize: function (v) {
            return v.name;
        },
        setPropStyle: function (v) {
            return v.name;
        },
        setPropPrice: function (v) {
            return '$' + v;
        },
        rowClassCB: function (data, index) {
            return 'p-l-0 m-l-0';
        }
    },
    components : {
        'vuetable' : vuetable
    }
});