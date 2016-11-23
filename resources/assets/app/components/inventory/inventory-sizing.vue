<template>
    <div>
        <div v-for="size_container in size_containers">
            <div class="box sizing_container"
                 v-bind:id="'size_'+size_container.id"
                 v-bind:data-container-id="size_container.id">
                <div class="box-body clearfix">
                    <div class="form-group row">
                        <label class="col-sm-3 form-control-label">Size</label>
                        <div class="col-sm-9">
                            <div class="btn-group-prpl" data-toggle="buttons">
                                <label class="form-control-label btn white" v-for="size in sizings" style="margin-right: 3px;">
                                    <input
                                            required
                                            aria-required="true"
                                            validation="required"
                                            type="radio"
                                            v-bind:name="'sizings['+size_container.id +'][size_id]'" id="option1"
                                            autocomplete="off"
                                            v-bind:value="size.id"> {{ size.name }}
                                </label>
                            </div>
                        </div>
                        <button type="button"
                                v-on:click="deleteSizeContainer(size_container)" style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0; opacity:.3" class=" m-l-1 pull-right btn white btn-xs text-muted "><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>

                    <div class="row row-horizon clearfix" >

                        <div class="col-sm-4 thumbnail-container"  v-for="(image, $index) in size_container.images">
                            <div class="box m-b-0">
                                <div class="item" >
                                    <div class="item-overlay active p-l p-r " style="z-index: 999;">
                                        <a type="button" style=""
                                           v-on:click="deleteSizeImage(size_container, image, $event)"
                                           v-bind:data-id="image.id"
                                           class="pull-right text-danger"><i class="fa fa-times fa-fw"></i></a>
                                        <span class="pull-left label dark-white text-color">{{ size_container.images.length - $index }}</span>
                                    </div>

                                    <div class="thumbnail">
                                        <img :src="image.location"
                                             :data-gallery="'image-thumbs_'+size_container.id" data-width="100%" class="w-full"
                                             data-toggle="lightbox" :data-remote="image.location"/>
                                    </div>
                                </div>
                                <input type="hidden"
                                       :name="'sizings['+size_container.id +'][images]['+image.key+'][data]'" :value="image.json" />
                                <div class="box-body m-t-0 p-t-0">
                                    <div class="image_item_component">
                                        <label class="text-muted text-sm m-b-0 p-b-0">Quantity:</label>
                                        <input type="text"
                                               value="1"
                                               v-bind:name="'sizings['+size_container.id+'][images]['+image.key+'][qty]'"
                                               class="text-center image_qty_btn"  />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="box-footer">
                    <div class="form-group categories_wrapper" style="display: none" >
                        <input type="text"
                               :disabled="size_container.images.length > 0"
                               v-bind:id="'categories_input_'+size_container.id"
                               v-bind:name="'sizings['+size_container.id+'][categories]'"
                               class="selectized"
                               placeholder="Type categories or keywords">
                    </div>
                    <div class="clearfix">
                        <div class="row">
                            <div class="col-sm-offset-3 col-sm-7">

                                        <span class="pull-left">
                                            <image-attach :s3_key_url="s3_key_url" :user_hash="user_hash"></image-attach>
                                        </span>

                                <button type="button" class="pull-left btn white btn-sm "
                                        :disabled="size_container.images.length == 0"
                                        @click="toggleCategory" >Categories</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
   import FileUpload from '../FileUpload.vue';

    export default{
        props: ["size_containers", "user_hash", "s3_key_url"],
        data: function () {
            return {
                size : {images: [{}],id : null},
                size_container : null,
                sizings: [],
                inventory_types: KABOOODLE_APP.inventory_types
            }
        },
        created : function () {
            console.log('Size component ready');

            var scope = this;

            $Bus.$on('size-container:added', function(sizeContainerData) {
                scope.$nextTick(function(){
                    $('#categories_input_'+sizeContainerData.id).selectize({
                        delimiter: ',',
                        persist: false,
                        plugins: ['remove_button'],
                        create: function (input) {
                            return {
                                value: input,
                                text: input
                            }
                        }
                    });
                });
            });

            $Bus.$on('style-changed', function(id) {
                var styleSizes = scope.getStyleSizes(id);
                if (styleSizes.length > 0) {
                    scope.setSizings(styleSizes);
                }
            });

            $Bus.$on('add-size', function() {
                scope.addSizeContainer();
            });

            $Bus.$on('image:deleted', function(size_container, image) {
                if (size_container.images.length == 0) {
                    var $wrapperEl =  $('#size_'+size_container.id).find('.categories_wrapper');
                    $wrapperEl.hide();
                    $wrapperEl.find('input:first-of-type').addClass('disabled').prop('disabled', true);
                }
            });

            $Bus.$on('image:uploaded', function(el, image) {
                image.json = JSON.stringify(image);
                var sizeEl = el.closest('.sizing_container'),
                        sizeContainerId = sizeEl.data('container-id');

                var container = ($.findFirst(scope.size_containers, function(obj) {
                    return obj.id == sizeContainerId;
                }));

                container.images.unshift(image);
                setTimeout(function(){
                    $('#size_'+container.id).find("input.image_qty_btn").TouchSpin({
                        min: 1
                    });
                },0);
            });

            this.addSizeContainer();
        },
        methods : {
            setSizings : function(sizings){
                this.sizings = sizings;
            },
            getStyleSizes : function(styleId) {
                var style = $.grep(this.inventory_types[0].styles, function(e){
                    return parseInt(e.id) === parseInt(styleId);
                });

                if (style.length > 0 ) {
                    return style[0].sizes;
                }

                return [];
            },
            deleteSizeImage : function(size_container, img) {
                var that = this;
                var index = size_container.images.indexOf(img);
                if (index != -1) {
                    size_container.images.splice(index, 1);
                    $Bus.$emit('image:deleted', size_container, img);
                }
            },
            createSizeObject : function() {
                var rand = Math.random().toString(36).slice(2);
                this.setSizings(this.getStyleSizes($('#inventory-styles-el').val()));
                return {id: rand, images: []};
            },
            addSizeContainer : function() {
                var sizeContainerData = this.createSizeObject();
                this.size_containers.push(sizeContainerData);
                $Bus.$emit('size-container:added', sizeContainerData);
            },
            deleteSizeContainer : function(size) {
                var that = this;
                confirmModal(function($noty){
                    var index = that.size_containers.indexOf(size);
                    if (index != -1) {
                        that.size_containers.splice(index, 1);
                    }
                    $Bus.$emit('size-container:removed', size);
                    $noty.close();
                });
            },
            toggleCategory : function(e) {
                e.preventDefault();
                var $el = $(e.target),
                        $categoryWrapperEl = $el.closest('.box-footer').find('.categories_wrapper');
                if ($categoryWrapperEl.is(':visible')) {
                    $categoryWrapperEl.hide();
                } else {
                    $categoryWrapperEl.show();
                    $categoryWrapperEl.find('input:first-of-type').prop('disabled', false).removeClass('disabled');
                }
            },

        },
        components: {
            'image-attach' : FileUpload
        }
    }
</script>
