<template>
    <div class="form-group row " :class="errors ? 'has-error has-danger' : null">
        <label class="col-sm-3 control-label form-control-label">
            <slot name="label"></slot>
        </label>
        <div class="col-sm-9 ">
            <slot name="input"></slot>
        </div>
    </div>
</template>
<script>
    export default{
        props:{
            errors: null
        }
    }
</script>
