<template>
    <li
            :data-id="id"
            class="list-group-item lt box-shadow-z0 b"
    >
        <a :href="ref_url">
        <span class="pull-left m-r" v-if="img_src">
            <img :src="img_src" class="w-40 img-circle">
        </span>
        <span class="clear block">
            {{ title }}
            <small class="text-muted block">
                <timestamp :timestamp="created_at"></timestamp>
            </small>
        </span>
        </a>
    </li>
</template>
<script>
    import Timestamp from '../Timestamp.vue';
    export default{
        props: {
            id : null,
            model : null,
            img_src : null,
            is_read : null,
            ref_url : {
                default : null
            },
            priority : {
                type: String
            },
            title : {
                required: true,
                type: String
            },
            created_at : {
                required: true,
            }
        },
        data: function() {
            return {
                notices: []
            }
        },
        created : function() {

        },
        components: {
            'timestamp' : Timestamp
        }
    }
</script>