<template>
    <div :id="'size_'+id" class="sizing_container" :data-id="id">
        <div class="box-body clearfix " >
            <div class="form-group sizing-row row">
                <label class="col-sm-3 form-control-label">Size</label>
                <div class="col-sm-9">
                    <div class="btn-group-prpl" data-toggle="buttons">
                        <label class="form-control-label btn white" :key="size.id" v-for="size in sizings" style="margin-right: 3px;">
                            <input
                                    required
                                    aria-required="true"
                                    validation="required"
                                    type="radio"
                                    :name="'sizings['+id +'][size_id]'" id="option1"
                                    autocomplete="off"
                                    :value="size.id"> {{ size.name }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="row row-horizon clearfix" style="position: relative; overflow: auto; ">
                <div class="col-sm-4 thumbnail-container m-b-2"  v-for="(image, $index) in images" :key="id+'_'+image.id">
                    <div class="box no-shadow m-b-0 p-t-1 p-b-1 r b-1 b b-a" >
                        <div class="item" >
                            <div class="item-overlay active p-l p-r " style="z-index: 999;">
                                <a type="button" style=""
                                   @click="deleteImage(image, $event)"
                                   :data-id="image.id"
                                   class="pull-right text-danger"><i class="fa fa-times fa-fw"></i></a>
                                <span class="pull-left label dark-white text-color">{{ images.length - $index }}</span>
                            </div>

                            <div class="thumbnail">
                                <img :src="image.location"
                                    data-width="100%" class="w-full"
                                     :data-remote="image.location"/>
                            </div>
                        </div>
                        <input type="hidden"
                               :name="'sizings['+id +'][images]['+image.key+'][data]'" :value="image.json" />
                        <div class="box-body m-t-0 p-b-0 p-t-0">
                            <div class="image_item_component">
                                <label class="text-muted text-sm m-b-0 p-b-0">Quantity:</label>
                                <input type="text"
                                       value="1"
                                       :name="'sizings['+id+'][images]['+image.key+'][qty]'"
                                       class="text-center image_qty_btn"  />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-footer">
            <div class="form-group categories_wrapper">
                <multiselect
                        v-if="images.length > 0 && display.categories"
                        v-model="category"
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
                <template v-for="category in categories">
                    <input type="hidden"
                           :value="category.name"
                           :name="'sizings['+id+'][categories][]'"
                           class="selectized">
                </template>
            </div>
            <div class="clearfix">
                <div class="row">
                    <div class="col-sm-offset-3 col-sm-7">
                                        <span class="pull-left add-images-btn">
                                            <image-attach
                                                    :ukey="id"
                                                    :s3_key_url="s3_key_url" ></image-attach>
                                        </span>

                        <button type="button" class="pull-left btn add-categories-btn white btn-sm "
                            :disabled="images.length == 0"
                                @click="toggleCategory" >Categories</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="./../../multiselect/vue-multiselect.min.css"></style>
<script>
    import FileUpload from  '../../FileUpload.vue';
    import Multiselect from 'vue-multiselect';

    export default{
        props: {
            id: {
                require: true,
            },
            s3_key_url: {
                required: true,
                type: String
            },
            sizings: {
                require: true,
                type: Array
            },
        },
        data(){
            return{
                display: {
                    categories: false,
                },
                images: [],
                categories: [],
                category: [],
            }
        },
        mounted(){
            this.$nextTick(()=>{
                $('.row-horizon').perfectScrollbar();
            });

            $Bus.$on('image:uploaded:'+this.id, (el, responseData)=>{
                alert('image uploaded');
                this.images.unshift(responseData);

                this.$nextTick(()=>{
                    $('.row-horizon').perfectScrollbar('update');
                    $('#size_'+this.id).find("input.image_qty_btn").TouchSpin({
                        min: 1
                    });
                });
            });
        },
        methods:{
            toggleCategory(){
                this.display.categories == true ? this.display.categories = false : this.display.categories = true;
            },
            removeTag(option) {
                let index = this.categories.indexOf(option);
                if (index > -1) {
                    this.categories.splice(index, 1);
                }
            },
            addTag(newTag){
                const tag = {
                    name: newTag,
                }

                this.categories.push(tag)
                this.category.push(tag)
            },
            deleteImage(image, event){
                let index = this.images.indexOf(image);
                if (index > -1) {
                    this.images.splice(index, 1);
                }
            }
        },
        components:{
            'multiselect' : Multiselect,
            'image-attach' : FileUpload,
        }
    }
</script>

