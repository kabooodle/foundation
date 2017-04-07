<template>
    <div class="inline m-r-sm">
        <span
            class="avatar_container _80 avatar-thumbnail">
            <span class="text-sm">{{ listable.name }}</span>
            <img :src="listable.cover_photo">
        </span>
        <div class="block" style="margin: -6px 2px 0 0">
            <span class="text-right"  @click="removeItem">
                <i class="fa fa-times text-danger pointer"></i>
            </span>
        </div>
    </div>
</template>
<style>
    
</style>
<script>
    export default {
        props: {
            listable: {
                type: Object,
                required: true
            }
        },
        data () {
            return {}
        },
        methods: {
            removeItem() {
                this.$emit('remove-item');
            }
        }
    }
</script>
