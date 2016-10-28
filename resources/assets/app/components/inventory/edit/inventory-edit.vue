<template>
    <div>
        <div class="row">
            <div class="col-md-3">
                <input
                        v-for="image in images"
                        type="hidden"
                        :name="'images['+image.id+'][data]'"
                        :value="image.json">
            </div>
            <div class="col-md-9">

            </div>
        </div>

    </div>
</template>
<script>
    import FileUpload from '../../FileUpload.vue';

    export default{
        components: {
            'image-attach' : FileUpload
        }
    }
</script>
