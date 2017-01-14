<template>
    <div class="box white">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-2 m-t-lg">
                    <h6 class="m-a-0">
                        <a>
                            <span class="text-muted text-sm">{{ style.name }} <i class="fa fa-angle-down"></i></span>
                        </a>
                        <small class="text-sm text-muted block">({{ style.total }})</small>
                    </h6>
                </div>
                <div class="col-sm-10 m-t-2">
                    <div class="pull-left btn-group-prpl">
                        <style-size-button
                                v-for="size in style.sizes"
                                :name="size.name"
                                :count="size.items.length"
                                :aria-pressed="(selectedItems.find(s => s.style_id === style.id && s.size_id === size.id ) ? ' true ' : null )"
                                :class="[ selectedItems.find(s => s.style_id === style.id && s.size_id === size.id )  ? 'sex active' : '' ] "
                                @click="( selectedItems.find(s => s.style_id === style.id && s.size_id === size.id ) ? removeSizes(style, size, size.items, $event) : addSizes(style, size, size.items, $event) )"
                        ></style-size-button>
                        <style-size-button name="ALL" :count="style.sizes.length"></style-size-button>
                    </div>
                </div>
             </div>
        </div>
    </div>
</template>
<script>
    import StyleSizeButton from './StyleSizeButton.vue';
    export default{
        data(){
            return{

            }
        },
        components:{
            'style-size-button' : StyleSizeButton
        }
    }
</script>
