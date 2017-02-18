<template>
    <div>
        <div class="col-sm-3">
            <div class="box white">
                <div class="box-body">
                    <image :src=grouping.coverimage.location style="width: 100%"></image>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Spinny from '../Spinner.vue';

    export default {
        props: {
            grouping: {
                type: Object,
                required: true,
            },
        },
        data() {
            return {
                msg: 'hello vue'
            }
        },
        components: {
            'spinny': Spinny,
        }
    }
</script>
