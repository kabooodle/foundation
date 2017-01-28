<template>
    <div>
        <div class="box" v-for="size_container in size_containers" >
            <button type="button" @click="deleteSizeContainer(size_container, $event)" style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0; opacity:.3" class=" m-l-1 pull-right btn white btn-xs text-muted "><i class="fa fa-times" aria-hidden="true"></i></button>
            <size-container
                    :images="size_container.images"
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

            $Bus.$on('image:uploaded', (el, responseData)=>{

                responseData.json = JSON.stringify(responseData);
                var sizeEl = el.closest('.sizing_container'),
                        sizeContainerId = sizeEl.data('id');

                var container = ($.findFirst(this.size_containers, function(obj) {
                    return obj.id == sizeContainerId;
                }));

                container.images.unshift(responseData);

                this.$nextTick(()=>{
                    $('#size_'+sizeContainerId).find("input.image_qty_btn").TouchSpin({
                        min: 1
                    });
                });
            });

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
