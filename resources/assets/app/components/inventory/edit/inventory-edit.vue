<template>
    <div>
        <div class="form-group row">
            <label class="col-sm-3 form-control-label">Added</label>
            <div class="col-sm-6">
                <p style="margin-top: 6px;" class="m-b-0">
                    <timestamp :timestamp="item.created_at"></timestamp>
                </p>
            </div>
        </div>
        <div class="form-group row">
            <label for="style_id" class="col-sm-3 form-control-label">Style</label>
            <div class="col-sm-5">
                <select name="style_id" id="style-el" class="form-control" @change="changeStyle">
                    <option
                            v-for="style in styles"
                            :key="style.id"
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
                            :key="size.id"
                            :value="size.id"
                            :selected="item.style_size.id == size.id">{{ size.name }}
                </select>
            </div>
        </div>
        <div class="form-group row ">
            <label for="price_usd" class="col-sm-3 form-control-label">Wholesale Price in USD$</label>
            <div class="col-sm-3">
                <input type="number" v-model="wholesale_price_usd" name="wholesale_price_usd" :value="item.wholesale_price_usd_less_5_percent" id="inventory-wholesale-el" class="form-control float" step="any" placeholder="0.00">
            </div>
        </div>
        <div class="form-group row ">
            <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
            <div class="col-sm-3">
                <input type="number" v-model="price_usd" id="inventory-price-el" name="price_usd" :value="item.price_usd" class="form-control float" step="any" min="0" placeholder="0.00">
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
            <label for="description" class="col-sm-3 form-control-label">Description</label>
            <div class="col-sm-7">
                <textarea class="form-control" name="description" rows="2">{{ item.description }}</textarea>
                <small class="block text-muted text-sm">(Optional)</small>
            </div>
        </div>
        <div class="form-group row">
            <label for="categories" class="col-sm-3 form-control-label">Categories</label>
            <div class="col-sm-7">
                <multiselect
                        v-model="category_value"
                        tag-placeholder="Add this as a new category"
                        placeholder="Add categories"
                        label="name"
                        track-by="name"
                        :options="categories"
                        :multiple="true"
                        :taggable="true"
                        @remove="removeTag"
                        @tag="addTag">
                </multiselect>
                <small class="block text-muted text-sm">(Optional)</small>
                <template v-for="category in categories"><input type="hidden" name="categories[]" :value="category.name"></template>
            </div>
        </div>

        <hr>
        <div class="form-group row m-t-md">
            <div class="col-sm-12">
                    <div class="box inline p-a-sm b b-a no-shadow r"
                         :class="cover_photo == image.id ? 'b-primary' : null "
                         v-for="image in images" style="margin-right:.78rem; margin-bottom:.78rem;"
                        :key="image.id"
                    >
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
                                    :name="'images[]'"
                                    :value="image.json">
                        </span>
                        <button
                                @click="setCoverPhoto(image.id, $event)"
                                :disabled="cover_photo == image.id"
                                :class="cover_photo == image.id ? true: false"
                                class="btn white btn-xs block text-center center-block"
                        >
                            <span v-if="cover_photo == image.id">Cover photo</span>
                            <span v-else>Make cover</span>
                        </button>
                    </div>
            </div>
        </div>

        <input type="hidden" v-model="cover_photo" name="cover_photo" :value="cover_photo">

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <!--<div class="clearfix">-->

                <button
                        type="submit"
                        class="btn pull-left primary block"
                        :disabled="processing || archiving || activating"
                        :class="processing ? 'disabled' : null"
                        @click="validateForm(item, $event)"> Save <spinny v-if="processing"></spinny>
                </button>
                <span class="pull-left m-l-sm">
                    <image-attach
                        :disabled="processing || archiving || activating"
                        btn-class-size=""
                        :user_hash="item.user.public_hash"
                        :s3_bucket="s3_bucket"
                        :s3_acl="s3_acl"
                        :s3_key_url="api_route"
                        multiple="true"></image-attach>
                </span>

                <button
                        v-if="archived"
                        type="button"
                        class="btn m-l-xs pull-left white block"
                        :disabled="processing || activating"
                        :class="processing ? 'disabled' : null"
                        @click="activateItem(item, $event)"> Unarchive <spinny v-if="activating"></spinny>
                </button>
                <button
                        v-if="!archived"
                        type="button"
                        class="btn m-l-xs pull-left white block"
                        :disabled="processing || archiving"
                        :class="processing ? 'disabled' : null"
                        @click="archiveItem(item, $event)"> Archive <spinny v-if="archiving"></spinny>
                </button>
                <!--</div>-->
            </div>
        </div>
    </div>
</template>
<style src="./../../multiselect/vue-multiselect.min.css"></style>
<script>
    import Multiselect from 'vue-multiselect';
    import FileUpload from '../../FileUpload.vue';
    import Timestamp from '../../Timestamp.vue';
    import Spinny from  '../../Spinner.vue';

    export default{
        props: ["styles", "existingimages", "item", "tags", "s3_bucket", "s3_acl", "api_route", "archive_endpoint"],
        data : function() {
            return {
                processing: false,
                categories: [],
                category_value: [],
                images : [],
                sizes : [],
                selected_style : '',
                wholesale_price_usd : null,
                price_usd : null,
                cover_photo: null,
                archived : false,
                activating: false,
                archiving: false,
            }
        },
        watch : {
            images: function(){
                $Bus.$emit('images:changed', this.images);
                if (this.images.length == 1) {
                    this.cover_photo = this.images[0].id;
                }
            }
        },
        created(){
            if (this.item.archived_at) {
                this.archived = true;
            }

            this.cover_photo = this.item.cover_photo_file_id;

            if(this.tags && this.tags != '') {
                _.each(this.tags.split(','), (tag)=>{
                    this.addTag(tag);
                });
            }

            const scope = this;
            this.wholesale_price_usd = this.item.wholesale_price_usd;
            this.price_usd = this.item.price_usd;

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
        },
        methods : {
            setCoverPhoto(imageKey, event){
                event.preventDefault();
                this.cover_photo = imageKey;
            },
            removeTag(option) {
                this.categories.splice(this.categories.indexOf(option));
            },
            addTag (newTag) {
                const tag = {
                    name: newTag,
                }
                this.categories.push(tag)
                this.category_value.push(tag)
            },
            setSizes : function(sizes){
                this.sizes = sizes;
            },
            setSelectedStyle : function(style){
                this.selected_style = style;
            },
            getSelectedStyleId(){
              return parseInt($('#style-el').val());
            },
            updateDefaultPricings(){
                let styleId = this.getSelectedStyleId();
                let style = this.getStyleById(styleId);
                let ws_price = moneyfy(style.wholesale_price_usd_less_5_percent);
                let suggested_price = moneyfy(style.suggested_price_usd);
                $('#inventory-wholesale-el')
                    .val(ws_price)
                    .prop('placeholder', ws_price);

                $('#inventory-price-el')
                        .val(suggested_price)
                        .prop('placeholder', suggested_price);

                this.price_usd = suggested_price;
                this.wholesale_price_usd = ws_price;
            },
            // Iterates over styles and returns single item
            getStyleById: function(id) {
                return _.find(this.styles, {id: id});
            },
            changeStyle: function(e){
                var $el = $(e.target),
                        id = parseInt($el.val()),
                        style = this.getStyleById(id);

                this.setSelectedStyle(style);
                this.setSizes(style.sizes);
                this.updateDefaultPricings();
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
            },
            validateForm: function (item, event) {
                event.preventDefault();
                this.processing = true;
                const scope = this;
                let $form = $(event.target).closest('form');
                let btn = $(event.target);
                let btnHtml = btn.html();

                if (this.images.length == 0) {
                    notify({text:  'Must have at least 1 image'});
                    return false;
                    this.processing = false;
                }

                this.$http.put($form.prop('action'), $form.serializeObject()).then(function(response){
                    notify({text:  response.body.data.msg, type: 'success'});
                    $Bus.$emit('inventory-item:updated', scope.item, JSON.parse(response.body.data.item));
                    return false;
                }, function(response){
                    notify({text:  response.body.data.msg});
                    return false;
                }).finally(function(){
                    this.processing = false;
                });
            },
            archiveItem(item, event){
                event.preventDefault();
                const scope = this;
                let $form = $(event.target).closest('form');
                let action = this.archive_endpoint.replace('::ID::', item.id);
                this.archiving = true;
                this.$http.put(action, $form.serializeObject()).then(function(response){
                    notify({text:  response.body.data.msg, type: 'success'});
                    this.archived = true;
                    $Bus.$emit('inventory-item:updated', scope.item, JSON.parse(response.body.data.item));
                }, function(response){
                    notify({text:  response.body.data.msg});
                }).finally(function(){
                    this.archiving = false;
                });
            },
            activateItem(item, event){
                event.preventDefault();
                const scope = this;
                let $form = $(event.target).closest('form');
                let action = this.archive_endpoint.replace('::ID::', item.id);
                this.activating = true;
                this.$http.delete(action, $form.serializeObject()).then(function(response){
                    notify({text:  response.body.data.msg, type: 'success'});
                    this.archived = false;
                    $Bus.$emit('inventory-item:updated', scope.item, JSON.parse(response.body.data.item));
                }, function(response){
                    notify({text:  response.body.data.msg});
                }).finally(function(){
                    this.activating = false;
                });
            },
        },
        components: {
            'image-attach' : FileUpload,
            'timestamp' : Timestamp,
            'multiselect' : Multiselect,
            'spinny' : Spinny
        }
    }
</script>
