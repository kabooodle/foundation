<template>
    <div>
        <div class="form-group row">
            <label for="style_id" class="col-sm-3 form-control-label">Style</label>
            <div class="col-sm-7">
                <select name="style_id" class="form-control" @change="changeStyle">
                    <option
                            v-for="style in styles"
                            :value="style.id"
                            :selected="selected_style.id == style.id">{{ style.name }}
                </select>
            </div>
        </div>
        <div class="form-group row ">
            <label for="size_id" class="col-sm-3 form-control-label">Size</label>
            <div class="col-sm-7">
                <select name="size_id" class="form-control" id="form_size_el">
                    <option
                            v-for="size in sizes"
                            :value="size.id"
                            :selected="item.style_size.id == size.id">{{ size.name }}
                </select>
            </div>
        </div>
        <div class="form-group row ">
            <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
            <div class="col-sm-7">
                <input type="number" name="price_usd" :value="item.price_usd" class="form-control float" step="any" min="0" placeholder="0.00">
            </div>
        </div>
        <div class="form-group row">
            <label for="initial_qty" class="col-sm-3 form-control-label">Available Quantity</label>
            <div class="col-sm-7">
                <input type="number" name="initial_qty" :value="item.initial_qty" class="form-control number" >
            </div>
        </div>
        <div class="form-group row ">
            <label for="description" class="col-sm-3 form-control-label">Description
                <small class="block text-muted text-sm">(Optional)</small>
            </label>
            <div class="col-sm-7">
                <textarea class="form-control" name="description" rows="2">{{ item.description }}</textarea>
            </div>
        </div>
        <div class="form-group row">
            <label for="categories" class="col-sm-3 form-control-label">Categories
                <small class="block text-muted text-sm">(Optional)</small>
            </label>
            <div class="col-sm-7">
                <input type="text" name="categories" class="selectized" id="tags" placeholder="Type categories" :value="tags">
            </div>
        </div>
        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <span class="pull-left">
                    <template v-for="image in images">
                        <img :src="image.location" height="64" width="64">
                           <input
                                   type="hidden"
                                   :name="'images['+image.id+'][data]'"
                                   :value="image.json">
                    </template>
                <span>
            </div>
        </div>

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <span class="pull-left">
                    <image-attach
                            btn-class-size=""
                            :user_hash="item.user.public_hash"
                            :s3_key_url="api_route"
                            multiple="false"></image-attach>
                </span>
                <button type="submit" class="btn primary">Save</button>
            </div>
        </div>

    </div>
</template>
<script>

//    require('../../../../vendor/fileupload/js/vendor/load-image');
//    require('../../../../vendor/fileupload/js/vendor/canvas-to-blob.min');
//    require('../../../../vendor/fileupload/js/jquery.fileupload');
//    require('../../../../vendor/fileupload/js/jquery.fileupload-process');
//    require('../../../../vendor/fileupload/js/jquery.fileupload-image');
//    require('../../../../vendor/fileupload/js/jquery.fileupload-ui');

    import FileUpload from '../../FileUpload.vue';

    export default{
        props: ["styles", "item", "tags", "api_route"],
        data : function() {
            return {
                images: [],
                sizes : [],
                selected_style : '',
                categories : '',
            }
        },
        methods : {
            getStyleById: function(id) {
                return $.findFirst(this.styles, function(obj) {
                    return obj.id == id;
                });
            },
            setSizes : function(sizes){
                this.sizes = sizes;
            },
            setSelectedStyle : function(style){
                this.selected_style = style;
            },
            changeStyle: function(e){
                var $el = $(e.target),
                        id = parseInt($el.val()),
                        style = this.getStyleById(id);
                this.setSelectedStyle(style);
                this.setSizes(style.sizes);
            },
            deleteImage: function(image){
                let index = this.images.indexOf(image);
                if(index > 0 ) {
                    this.images.splice(index,1);
                }
            },
            insertImage: function(image) {
                // Push images to front of array so we can iterate newest to oldest.
                this.images.unshift(image);
            }
        },
        components: {
            'image-attach' : FileUpload
        },
        created : function(){
            const scope = this;

            $Bus.$on('image:uploaded', function(el, image){
                scope.insertImage(image);
            });

            this.setSelectedStyle(this.item.style);
            this.setSizes(this.item.style.sizes);

            setTimeout(function(){
                $('.selectized').selectize({
                    delimiter: ',',
                    persist: false,
                    valueField: 'tag',
                    labelField: 'tag',
                    searchField: 'tag',
                    plugins: ['remove_button'],
                    create: function (input) {
                        return {
                            tag: input
                        }
                    }
                });
            });
        }
    }
</script>
