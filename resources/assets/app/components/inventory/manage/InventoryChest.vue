<template>
    <div class="box white">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-2 m-t-lg">
                    <h6 class="m-a-0">
                        <a>
                            <span class="text-muted text-sm">{{ item.name }} <i class="fa fa-angle-down"></i></span>
                        </a>
                        <small class="text-sm text-muted block">({{ item.total }})</small>
                    </h6>
                </div>
                <div class="col-sm-10 m-t-2">
                    <div class="pull-left btn-group-prpl">
                        <!--<style-size-button-->
                                <!--v-for="size in item.sizes"-->
                                <!--:name="size.name"-->
                                <!--:count="size.items.length"-->
                                <!--:aria-pressed="(selectedItems.find(s => s.style_id === item.id && s.size_id === size.id ) ? ' true ' : null )"-->
                                <!--:class="[ selectedItems.find(s => s.style_id === item.id && s.size_id === size.id )  ? 'sex active' : '' ] "-->
                                <!--@click="( selectedItems.find(s => s.style_id === item.id && s.size_id === size.id ) ? removeSizes(style, size, size.items, $event) : addSizes(style, size, size.items, $event) )"-->
                        <!--&gt;</style-size-button>-->
                        <!--<style-size-button name="ALL" :count="item.sizes.length"></style-size-button>-->
                    </div>
                </div>
             </div>
        </div>
    </div>
</template>
<script>
    import ChestDrawer from './ChestDrawer.vue';
    import StyleSizeButton from './StyleSizeButton.vue';
    export default{
        props: {
            item: {
                required: true,
                type: Object
            }
        },
        data(){
            return{
                active: false,
            }
        },
        components:{
            'chest-drawer' : ChestDrawer,
            'style-size-button' : StyleSizeButton
        }
    }
</script>
