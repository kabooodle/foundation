<template>
    <div class="form-group row " :class="has_error ? 'has-error has-danger' : null">
        <label class="col-sm-3 control-label form-control-label">
            <slot name="label"></slot>
        </label>
        <slot name="input" class="">
            <span v-if="has_error" class="text-danger block text-xs">{{ has_error }}</span>
        </slot>
    </div>
</template>
<script>
    export default{
        props:{
            errors: {
                default: null,
            },
            field_name: {
                type: String,
            },
            use_error_helper: {
                type: Boolean,
                default: true
            }
        },
        computed: {
            has_error(){
                if (this.use_error_helper) {
                    let error_obj = this.errors && this.errors.hasOwnProperty(this.field_name) ? this.errors[this.field_name] : false;
                    return error_obj ? error_obj[0] : false;
                }
                return false;
            }
        }
    }
</script>
