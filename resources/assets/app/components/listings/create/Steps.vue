<template>
    <div class="box-footer box-buttons dker clearfix">
        <template v-if="current_step == 1">
            <button
                    :class="selected_listables_count == 0 ? 'disabled' : null"
                    :disabled="selected_listables_count == 0"
                    @click.prevent="$parent.gotoStepTwo"
                    class="btn white btn-sm pull-right">Continue <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></i></button>
        </template>

        <template  v-if="current_step == 2">
            <button
                    @click.prevent="$parent.gotoStepOne"
                    class="btn white btn-sm pull-left"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back to inventory</button>
            <button
                    :class="selected_sales_count == 0 ? 'disabled' : null"
                    :disabled="selected_sales_count == 0"
                    @click.prevent="$parent.gotoStepThree"
                    class="btn white btn-sm pull-right">Preview listing <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
        </template>

        <template  v-if="current_step == 3">
            <button
                    :disabled="disabled"
                    :class="disabled? 'disabled' : null"
                    @click.prevent="$parent.gotoStepTwo"
                    class="btn white btn-sm pull-left"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back to sales</button>
            <button
                    :disabled="disabled"
                    :class="disabled? 'disabled' : null"
                    @click.prevent="$parent.saveListing"
                    class="btn primary btn-sm btn-save-listing pull-right">Save listing
                <spinny v-if="disabled"></spinny>
            </button>
        </template>
    </div>
</template>
<script>
    import Spinny from '../../Spinner.vue';
    export default{
        props : {
            current_step : {
                type: Number,
                required: true
            },
            selected_listables_count : {
                type: Number,
                required: true
            },
            selected_sales_count: {
                type: Number,
                required: true
            }
        },
        data(){
            return{
                disabled: false,
            }
        },
        created(){
            $Bus.$on('listings:saving', ()=>{
                this.disabled = true;
            });

            $Bus.$on('listings:saved', ()=>{
                this.disabled = false;
            });
        },
        components: {
            'spinny' : Spinny
        }
    }
</script>
