<template>
    <div>
        <div class="box"
             v-for="size_container in size_containers"
             :key="size_container.id" >
            <button
                    type="button"
                    @click="deleteSizeContainer(size_container, $event)"
                    style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0; opacity:.3"
                    class=" m-l-1 pull-right btn white btn-xs text-muted ">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
            <size-container
                    :s3_bucket="s3_bucket"
                    :s3_acl="s3_acl"
                    :s3_key_url="s3_key_url"
                    :sizings="sizings"
                    :id="size_container.id"
            ></size-container>
        </div>
    </div>
</template>
<script>
    import SizeContainer from './Size-Container.vue';
    export default{
        props: {
            s3_bucket: {
                required: true,
                type: String
            },
            s3_acl: {
                required: true,
                type: String
            },
            s3_key_url: {
                required: true,
                type: String
            }
        },
        data(){
            return{
                sizings: [],
                size_containers: [],
                inventory_types: KABOOODLE_APP.inventory_types
            }
        },
        created(){



            $Bus.$on('add-size', ()=>{
                this.addSizeContainer();
            });

            /**
             * When the style is changed, the available sizings change also.
             * Update the sizings.
             */
            $Bus.$on('style-changed', (styleId)=>{
                let styleSizes = this.getStyleSizes(styleId);
                if (styleSizes.length > 0) {
                    this.setSizings(styleSizes);
                }
            });
        },
        methods: {
            setSizings(sizings){
                this.sizings = sizings;
            },
            getStyleSizes(styleId) {
                let style = $.grep(this.inventory_types[0].styles, function(e){
                    return parseInt(e.id) === parseInt(styleId);
                });

                if (style.length > 0 ) {
                    return style[0].sizes;
                }

                return [];
            },
            createSizeObject() {
                let rand = Math.random().toString(36).slice(2);
                this.setSizings(this.getStyleSizes($('#inventory-styles-el').val()));
                return {id: rand, images: []};
            },
            addSizeContainer() {
                let sizeContainerData = this.createSizeObject();
                this.size_containers.push(sizeContainerData);
//                $Bus.$emit('size-container:added', sizeContainerData);
            },
            deleteSizeContainer(size) {
                confirmModal(($noty)=>{
                    let index = this.size_containers.indexOf(size);
                    if (index != -1) {
                        this.size_containers.splice(index, 1);
                    }
                    $Bus.$emit('size-container:removed', size);
                    $noty.close();
                });
            },
        },
        components:{
            'size-container' : SizeContainer
        }
    }
</script>
<style>
    .ps-scrollbar-x {
        height: 11px !important;
    }
    .ps-scrollbar-x-rail {
        opacity: .9 !important;
    }
</style>
