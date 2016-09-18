<template>
    <form class="vue-stripe">
        <div class="form-row">
            <label>
                <span v-if="showLabels">Card Number</span>
                <input maxlength="19" type="text" @keyup="formatCard($event)" v-model="number" size="20" v-bind:class="[validationErrors.number]" class="card-number" placeholder="{{placeholders.number}}"/>
            </label>
        </div>
        <div class="form-row">
            <label>
                <span v-if="showLabels">Expiration (MM/YYYY)</span>
                <div class="styled-select" v-bind:class="[validationErrors.expMonth]">
                    <select v-model="expMonth" v-bind:class="[monthIsPlaceholder ? 'placeholder' : '']">
                        <option disabled selected value="">{{placeholders.month}}</option>
                        <option v-for="month in monthList">{{ month }}</option>
                    </select>
                </div>
            </label>
            <label>
                <div class="styled-select" v-bind:class="[validationErrors.expYear]">
                    <select v-model="expYear" v-bind:class="[yearIsPlaceholder ? 'placeholder' : '']">
                        <option disabled selected value="">{{placeholders.year}}</option>
                        <option v-for="year in yearList">{{ year }}</option>
                    </select>
                </div>
            </label>
            <label>
                <span v-if="showLabels">CVC</span>
                <input type="text" v-bind:class="[validationErrors.cvc]" v-model="cvc" size="4" class="cvc" placeholder="{{placeholders.cvc}}"/>
            </label>
        </div>
        <span transition="expand" v-if="stripeError" class="error-text">{{stripeError.message}}</span>
        <div v-if="showButton" class="order-now">
            <button class="btn orange" @click.prevent="$root.kachingCreateCardToken()">charge me</button>
        </div>
    </form>
</template>
<script>
    export default{
        props: {
            showButton: {
                "default": true
            },
            callback: {
                required: false
            },
            showLabels: {
                "default": false
            },
            devMode: {
                "default": false
            },
            shopUri: {
                required: false
            },
            card: {
                required: true,
                twoWay: true
            },
            stripeKey: {
                required: true
            }
        },
        data (){
            return {
                stripeError: null,
                number: "",
                cvc: "",
                expYear: "",
                expMonth: "",
                monthList: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                placeholders: {
                    year: 'Year',
                    month: 'Month',
                    cvc: 'CVC',
                    number: "Card Number"
                },
                validationErrors: {
                    number: "",
                    cvc: "",
                    expYear: "",
                    expMonth: ""
                }
            }
        },
        ready() {
            if (this.devMode) {
                this.number = "4242424242424242";
                this.cvc = "123";
                this.expYear = "19";
                return this.expMonth = '1';
            }
        },
        computed: {
            yearIsPlaceholder: function () {
                return this.expYear.length === 0;
            },
            monthIsPlaceholder: function () {
                return this.expMonth.length === 0;
            },
            yearList: function () {
                var num, today, years, yyyy;
                today = new Date;
                yyyy = today.getFullYear();
                years = (function () {
                    var i, ref, ref1, results;
                    results = [];
                    for (num = i = ref = yyyy, ref1 = yyyy + 10; ref <= ref1 ? i <= ref1 : i >= ref1; num = ref <= ref1 ? ++i : --i) {
                        results.push(num.toString().substr(2, 2));
                    }
                    return results;
                })();
                return years;
            },
            cardParams: function () {
                return {
                    number: this.number,
                    expMonth: this.expMonth,
                    expYear: this.expYear,
                    cvc: this.cvc
                };
            }
        },
        methods: {
            resetKaching: function () {
                console.log("resetting ");
                this.expMonth = "";
                this.cvc = "";
                this.expYear = "";
                this.number = "";
                return this.card = null;
            },
            formatCard: function (event) {
                var output;
                output = this.number.split("-").join("");
                if (output.length > 0) {
                    output = output.replace(/[^\d]+/g, '');
                    output = output.match(new RegExp('.{1,4}', 'g'));
                    if (output) {
                        return this.number = output.join("-");
                    } else {
                        return this.number = "";
                    }
                }
            },
            createToken: function () {
                Stripe.setPublishableKey(this.stripeKey);
                return Stripe.card.createToken(this.cardParams, this.createTokenCallback);
            },
            createTokenCallback: function (status, resp) {
                var uploadReq;
                this.validationErrors = {
                    number: "",
                    cvc: "",
                    expYear: "",
                    expMonth: ""
                };
                this.stripeError = resp.error;
                if (this.stripeError) {
                    if (this.stripeError.param === "number") {
                        this.validationErrors.number = "error";
                    }
                    if (this.stripeError.param === "exp_year") {
                        this.validationErrors.expYear = "error";
                    }
                    if (this.stripeError.param === "exp_month") {
                        this.validationErrors.expMonth = "error";
                    }
                    if (this.stripeError.param === "cvc") {
                        this.validationErrors.cvc = "error";
                    }
                }
                if (status === 200) {
                    this.card = resp;
                    if (this.callback) {
                        this.callback();
                    }
                    if (this.shopUri) {
                        return uploadReq = $.ajax({
                            method: 'post',
                            url: this.shopUri,
                            contentType: 'application/json; charset=utf-8',
                            dataType: "json",
                            data: JSON.stringify({
                                card: this.resp
                            })
                        });
                    }
                }
            }
        }
    }
</script>
