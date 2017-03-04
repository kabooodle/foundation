<template>
    <div
            class="col-md-3"
            :data-id="item.id"
            :data-size-id="listed_item.inventory_sizes_id"
            :data-style-id="listed_item.inventory_type_styles_id"
    >
        <div class="box p-a-xs p-b-0">
            <span class="item avatar_container block-center center _128h avatar-thumbnail">
                <div class="item-overlay active p-a-xs" style="z-index:999">
                    <followable
                            able_name="watchable"
                            unfollow_text="Watching"
                            follow_text="Watch"
                            show_on_hover="1"
                            btn_size_class="pull-right label dark-white text-color btn-xs"
                            able_type="Kabooodle\Models\ListingItems"
                            :able_id="'' + item.id"
                            :already_following="item.is_watched ? 1 : 0"
                            :endpoint="watch_endpoint"
                    ></followable>
                </div>
                <a :href="show_endpoint">
                    <img :src="listed_item.cover_photo.location" class="img-responsive">
                </a>
            </span>
            <div class="p-a-sm p-b-0">
                <div class="clearfix">
                    <h6 class="m-b-0">
                        <a class="" :href="show_endpoint">
                            {{ listed_item.name }}
                        </a>
                    </h6>
                    <div class="m-b-0 text-sm clearfix">
                        <div class="pull-left">
                            <div class="block">
                                <template v-if="listed_item.size">
                                    <span class="text-muted">Size:</span><span class="">{{ listed_item.size.name }}</span>
                                </template>
                                <template v-else>
                                    <span class="text-muted">Outfit</span>
                                </template>
                            </div>
                        </div>
                        <div class="pull-right" style="text-align: right">
                            <span class="text-muted ">Qty:</span> <span class="">{{ listed_item.available_quantity }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Followable from '../follow/Followable.vue';
    export default{
        props: {
            item : {
                required: true,
                type: Object,
            },
            listed_item: {
                required: true,
                type: Object,
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
        components:{
            'followable' : Followable
        }
    }
</script>
