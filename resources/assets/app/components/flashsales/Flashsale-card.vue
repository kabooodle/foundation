<template>
    <div class="box p-a-xs">
        <div class="item">
            <div class="item-overlay active p-a-xs" style="z-index:999">
                <a
                        v-if="flashsale.privacy == 'private'"
                        :href="show_endpoint"
                        class="pull-left text-u-c label danger label-md">
                    {{ flashsale.privacy }}
                </a>
                    <followable
                            able_name="watchable"
                            unfollow_text="Watching"
                            follow_text="Watch"
                            show_on_hover="1"
                            btn_size_class="pull-right  label dark-white text-color btn-xs"
                            able_type="Kabooodle\Models\Flashsales"
                            :able_id="'' + flashsale.id"
                            :already_following="flashsale.is_watched ? 'true' : 'false'"
                            :endpoint="watch_endpoint"
                    ></followable>
            </div>
            <div class="coverimage FlexEmbed FlexEmbed--3by1"
                 :style="'background-image: url('+flashsale.coverimage.location+')'"></div>
        </div>
        <div class="p-a-xs clearfix">
            <div class="text-muted ">
                <span class="text-xs">{{ daterange }}</span>
            </div>
            <div class="m-b-0 h-2x"><a :href="show_endpoint" class="_800">{{ flashsale.name }}</a></div>
            <div class="text-xs pull-right">
                <a :href="show_endpoint"><span class="_600">{{ flashsale.watchers.length }}</span> <span class="text-muted"> Watchers</span></a>
                <a class="m-l-sm" :href="show_endpoint"><span class="_600">{{ flashsale.sellers.length }}</span>  <span class="text-muted">Sellers</span></a>
                <a class="m-l-sm" :href="show_endpoint"><span class="_600">{{ flashsale.listing_items.length }}</span><span class="text-muted"> Items</span></a>
            </div>
        </div>
    </div>
</template>
<script>
    import Timestamp from '../Timestamp.vue';
    import Followable from '../follow/Followable.vue';
    export default{
        props: {
            flashsale: {
                type: Object,
                required: true,
            },
            watch_endpoint: {
                required: true,
                type: String
            },
            show_endpoint: {
                required: true,
                type: String
            },
        },
        data(){
            return{
                msg:'hello vue'
            }
        },
        computed: {
            daterange(){
                const format = 'MMM D h:mma';
                return moment(this.flashsale.starts_at).format(format)+' - '+ moment(this.flashsale.ends_at).format(format);
            }
        },
        components:{
            'timestamp' : Timestamp,
            'followable' : Followable
        }
    }
</script>
