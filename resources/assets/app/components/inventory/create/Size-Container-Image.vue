<template>
    <div class="col-sm-4 thumbnail-container m-b-2">
        <div class="box no-shadow m-b-0 p-t-1 p-b-1 r b-1 b b-a" >
            <div class="item" >
                <div class="item-overlay active p-l p-r " style="z-index: 999;">
                    <a type="button" style=""
                       @click="deleteImage(image, $event)"
                       :data-id="image.id"
                       class="pull-right text-danger"><i class="fa fa-times fa-fw"></i></a>
                    <span class="pull-left label dark-white text-color">{{ position }}</span>
                </div>

                <div class="thumbnail">
                    <img :src="image.location"
                         data-width="100%" class="w-full"
                         :data-remote="image.location"/>
                </div>
            </div>
            <input type="hidden"
                   :name="'sizings['+sizingId +'][images]['+image.key+'][data]'" :value="image.json" />
            <div class="box-body m-t-0 p-b-0 p-t-0">
                <div class="image_item_component">
                    <label class="text-muted text-sm m-b-0 p-b-0">Quantity:</label>
                    <input type="text"
                       v-model="quantity"
                       :name="'sizings['+sizingId+'][images]['+image.key+'][qty]'"
                       :id="quantityBtnId"
                       class="text-center image_qty_btn"  />
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    
</style>
<script>
    export default {
        props: {
            sizingId: {
                required: true,
                type: String,
            },
            quantity: {
                required: true,
            },
            position: {
                required: true,
                type: Number,
            },
            image: {
                required: true,
                type: Object,
            }
        },
        data () {
            return {
                quantityBtnId: 'image_qty_btn_'+this.image.hash,
            }
        },
        created() {

        },
    }
</script>
