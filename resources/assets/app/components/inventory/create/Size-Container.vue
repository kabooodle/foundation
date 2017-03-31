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
                <size-container-image v-for="(image, index) in images" :key="image.id"
                    :sizing-id="id"
                    :position="images.length - index"
                    :image="image"
                    :quantity="image.quantity"
                >
                </size-container-image>
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
                                :s3_bucket="s3_bucket"
                                :s3_acl="s3_acl"
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
    import SizeContainerImage from './Size-Container-Image.vue';
    import FileUpload from  '../../FileUpload.vue';
    import Multiselect from 'vue-multiselect';

    export default{
        props: {
            id: {
                require: true,
            },
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
            String.prototype.hashCode = function() {
                var hash = 0, i, chr;
                if (this.length === 0) return hash;
                for (i = 0; i < this.length; i++) {
                    chr   = this.charCodeAt(i);
                    hash  = ((hash << 5) - hash) + chr;
                    hash |= 0; // Convert to 32bit integer
                }
                return hash;
            };
            this.$nextTick(()=>{
                $('.row-horizon').perfectScrollbar();
            });

            $Bus.$on('image:uploaded:'+this.id, (el, responseData)=>{
                responseData.quantity = 1;
                responseData.hash = responseData.id.hashCode();
                this.images.unshift(responseData);

                this.$nextTick(()=>{
                    $('.row-horizon').perfectScrollbar('update');
                    $("input#image_qty_btn_"+responseData.hash).TouchSpin({
                        min: 1
                    }).change((data)=>{
                        var index = this.images.indexOf(responseData);
                        this.images[index].quantity = data.target.value;
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
            'size-container-image' : SizeContainerImage,
        }
    }
</script>

