<template>
    <div class="form-group row " :class="errors ? 'has-error has-danger' : null">
        <label class="col-sm-3 control-label form-control-label">
            <slot name="label"></slot>
        </label>
        <div class="col-sm-9">
            <slot name="input"></slot>
            <slot name="text-help"></slot>
            <template v-if="has_error">
                <span class="text-danger block text-xs">{{ errors[0] }}</span>
            </template>
        </div>
    </div>
</template>
<script>
    export default{
        props:{
            errors: {
                default: null,
                type: Object,
            },
            use_error_helper: {
                type: Boolean,
                default: true
            }
        },
        computed: {
            has_error(){
                return this.use_error_helper && this.errors && this.errors[0];
            }
        }
    }
</script>
