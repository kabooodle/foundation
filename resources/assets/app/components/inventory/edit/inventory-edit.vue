<template>
    <div>
        <div class="form-group row">
            <label class="col-sm-3 form-control-label">Added</label>
            <div class="col-sm-6">
                <p style="margin-top: 6px;" class="m-b-0">
                    <timestamp :timestamp="item.created_at.date"></timestamp>
                </p>
            </div>
        </div>
        <div class="form-group row">
            <label for="style_id" class="col-sm-3 form-control-label">Style</label>
            <div class="col-sm-5">
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
            <div class="col-sm-5">
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
            <div class="col-sm-3">
                <input type="number" name="price_usd" :value="item.price_usd" class="form-control float" step="any" min="0" placeholder="0.00">
            </div>
        </div>
        <div class="form-group row">
            <label for="initial_qty" class="col-sm-3 form-control-label">Available Quantity</label>
            <div class="col-sm-3">
                <input type="number" name="initial_qty" :value="item.initial_qty" class="form-control number" >
            </div>
        </div>
        <div class="form-group row">
            <label for="uuid" class="col-sm-3 form-control-label">Unique ID</label>
            <div class="col-sm-4">
                <input type="text" name="uuid" :value="item.uuid" class="form-control" required>
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
                    <div class="box inline p-a-sm m-r-1 m-b-1" v-for="image in images">
                         <div style="z-index: 999;" class="item-overlay active p-r-sm">
                                <a
                                        @click="deleteImage(image, $event)"
                                        type="button"
                                        class="pull-right text-danger"><i class="fa fa-times fa-fw"></i>
                                </a>
                            </div>
                        <span class="avatar_container _96 avatar-thumbnail">
                            <img
                                    data-toggle="lightbox"
                                    data-gallery="gallery"
                                    :data-remote="image.location"
                                    :src="image.location"
                            >
                            <input
                                    type="hidden"
                                    :name="'images['+image.id+'][data]'"
                                    :value="image.json">
                        </span>
                    </div>
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
    import FileUpload from '../../FileUpload.vue';
    import Timestamp from '../../Timestamp.vue';

    export default{
        props: ["styles", "existingimages", "item", "tags", "api_route"],
        data : function() {
            return {
                images : [],
                sizes : [],
                selected_style : '',
                categories : '',
            }
        },
        created : function(){
            const scope = this;

            if(this.existingimages.length){
                _.each(this.existingimages, function(image){
                    scope.images.push(image);
                });
            }

            // Bus event listener
            $Bus.$on('image:uploaded', function(el, image){
                scope.insertImage(image);
            });

            // set the selected style
            this.setSelectedStyle(this.item.style);

            // set the selected style' sizes
            this.setSizes(this.item.style.sizes);

            this.$nextTick(function(){
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
        },
        methods : {
            setSizes : function(sizes){
                this.sizes = sizes;
            },
            setSelectedStyle : function(style){
                this.selected_style = style;
            },
            // Iterates over styles and returns single item
            getStyleById: function(id) {
                return $.findFirst(this.styles, function(obj) {
                    return obj.id == id;
                });
            },
            changeStyle: function(e){
                var $el = $(e.target),
                        id = parseInt($el.val()),
                        style = this.getStyleById(id);

                this.setSelectedStyle(style);
                this.setSizes(style.sizes);
            },
            deleteImage: function(image, event){
                event.preventDefault();
                let scope = this;
                let index = scope.images.indexOf(image);
                if(index > -1 ) {
                    scope.images.splice(index,1);
                }
            },
            insertImage: function(image) {
                // Push images to front of array so we can iterate newest to oldest.
                this.images.unshift(image);
            }
        },
        components: {
            'image-attach' : FileUpload,
            'timestamp' : Timestamp
        }
    }
</script>
