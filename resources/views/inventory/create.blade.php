@extends('layouts.body_w_leftnav')

@section('body-content-left-nav')
    @include('inventory.partials._leftnav')
@endsection

@section('body-inner-content')
    @include('widgets._fileuploadscripts')

    <script>
        var inventoryTypes = {!!  $inventoryTypes->toJson()  !!}
    </script>

    <div id="inventory">

        {{ Form::open(['route' => ['shop.inventory.store', user()->username]]) }}

        <div class="box">
            <div class="box-header">
                <h2>Add Items to your Inventory</h2>
                <small>Once an item is in your inventory, you can add it to any sale, anytime!</small>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">

                <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                    <label for="type" class="col-sm-3 form-control-label">Type</label>
                    <div class="col-sm-7">
                        {{ Form::select('type', $inventoryTypes->pluck('name','id'), [], ['id' => 'inventory-type-el', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                    <label for="type" class="col-sm-3 form-control-label">Style</label>
                    <div class="col-sm-7">
                        {{ Form::select('styles', $inventoryTypes->first()->styles->pluck('name','id'), [], ['v-on:change' => 'styleChanged', 'id' => 'inventory-styles-el', 'class' => ' form-control ']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}">
                    <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
                    <div class="col-sm-7">
                        {{ Form::number('price_usd', null or 0, ['class' => 'form-control float', 'step' => 'any', 'min' => 0, 'placeholder' => '0.00']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                    <label for="description" class="col-sm-3 form-control-label">Description</label>
                    <div class="col-sm-7">
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2, 'placeholder'=> 'Optional']) }}
                    </div>
                </div>
            </div>
        </div>

        <inventory-sizing></inventory-sizing>

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <button type="submit" class="btn primary">Save</button>
                <button type="button" class="btn white" id="size-add-btn" v-on:click="addSizeContainer">Add Size</button>
            </div>
        </div>

        {{ Form::close() }}

        <script id="sizing-template" type="text/x-template">
            <div v-for="size_container in size_containers">
                <div class="box" id="size_@{{size_container.id}}">
                    <div class="box-body clearfix">
                        <div class="form-group row">
                            <label class="col-sm-3 form-control-label">Size</label>
                            <div class="col-sm-7">
                                <div class="btn-group-justified" data-toggle="buttons">
                                    <label class="btn white" v-for="size in size_container.sizing">
                                        <input type="radio" name="size[@{{ size_container.id }}][size_id]" id="option1"
                                               autocomplete="off" value="@{{ size.id }}"> @{{ size.name }}
                                    </label>
                                </div>
                            </div>
                            <button type="button" v-on:click="deleteSizeContainer(size_container)" style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0;" class="m-l-1 pull-right btn white btn-xs text-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>

                        <div class="row clearfix">
                            <div v-for="image in size_container.images">
                                <div class="col-sm-4 thumbnail-container">
                                    <div class="box">
                                        <div class="item" >
                                            <button type="button" style="z-index: 999; position: absolute; top: 0; right: 0; border: 0; border-radius: 0;" v-on:click="deleteSizeImage(size_container, image, $event)" data-id="@{{ image.id }}" class="pull-right btn btn-xs white text-danger"><i class="fa fa-trash fa-fw"></i></button>
                                            <div class="thumbnail">
                                                <img :src="image.location" data-gallery="image-thumbs" data-width="100%" class="w-full"  data-toggle="lightbox" data-remote="@{{ image.location }}"/>
                                            </div>
                                        </div>
                                        <input type="hidden" name="size[@{{ size_container.id }}][images][@{{ image.key }}][data]" value="@{{ image.json }}" />
                                        <div class="box-body m-t-0 p-t-0">
                                            <div class="image_item_component">
                                                <label class="text-muted text-sm m-b-0 p-b-0">Quantity:</label>
                                                <input type="text" value="1" name="size[@{{ size_container.id }}][images][@{{ image.key }}][qty]" class="text-center image_qty_btn"  />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <div class="form-group categories_wrapper" style="display: none">
                            <input type="text" id="categories_input_@{{ size_container.id }}" name="size[@{{ size_container.id }}][categories]" class="form-control selectized" placeholder="Type categories or keywords">
                        </div>
                        <div class="clearfix">
                            <div class="js-program_logo_upload_@{{size_container.id}}"></div>
                            <div class="pull-left upload-template_@{{size_container.id}}">
                                <button type="button" class="btn white btn-sm fileinput-button" style="display: inline-block;">
                                    Add Images<input type="file" name="file" class="js-s3_fileupload" accept='image/*' multiple/>
                                </button>
                                <button type="button" class="btn danger btn-sm js-cancel_button" style="display: none;">
                                    Cancel
                                    <div class="js-fileupload-progress fileupload-progress m-b-0 p-b-0" style="display: none;  margin-left: -9px; margin-right: -9px;">
                                        <div style="height: 8px;" class="progress progress-striped active m-b-0 p-b-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="5">
                                            <div class="progress-bar progress-bar-success" style="width: 5%;"></div>
                                        </div>
                                    </div>
                                </button>
                            </div>
                            <button type="button" class="m-l-1 pull-left btn white btn-sm " v-on:click="toggleCategory" >Add Categories</button>
                        </div>
                    </div>
                </div>
            </div>
        </script>

    </div>
@endsection

@push('footer-scripts')
<script>
    Vue.component('inventory-sizing', {
        template: '#sizing-template',
        data: function () {
            return {
                image: {size: '', key: '', location : '', bucket: ''},
                size : {images: [{}], sizing: [{}], id : null},
                size_containers : []
            }
        },
        events : {
            'style-changed' : function(id) {
                var styleObject = this.getStyleSizes(id);
                if (styleObject.length == 1) {
                    this.size.sizing.$add(styleObject);
                }
            },
            'add-size' : function() {
                this.addSizeContainer();
            },
            'image-uploaded' : function(size_container, image) {
                image.json = JSON.stringify(image);
                size_container.images.push(image);
                setTimeout(function(){
                    $('#size_'+size_container.id).find("input.image_qty_btn").TouchSpin({
                        min: 1
                    });
                }, 0);
            },
            'size-container:added' : function(sizeContainerData) {
                var that = this;
                setTimeout(function(){
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
                    $('.js-program_logo_upload_'+sizeContainerData.id).s3uploader({
                        save_file_model: false,
                        multiple: true,
                        s3_bucket: 'kabooodle-storage',
                        s3_key_url: '{{ route('api.files.sign') }}',
                        s3_key_payload: {
                            user: '{{ user()->public_hash }}'
                        },
                        templateEl: $('.upload-template_'+sizeContainerData.id),
                        fileupload_options: {},
                        on_s3_upload: function (data, textStatus, jqXHR) {
                            if ($.inArray(jqXHR.status, [201,200]) == -1) {
                                alert('An error occurred with an image, please try that image again.');
                                return false;
                            }
                            var xml = $(data);

                            that.$emit('image-uploaded', sizeContainerData, {
                                id:         xml.find('Key').text(),
                                bucket:     xml.find('Bucket').text(),
                                key:        xml.find('Key').text(),
                                location:   xml.find('Location').text()
                            });
                        },
                        on_file_add: function (element, data) {
                            if (data.files[0].type.indexOf("image")==-1) {
                                alert('File must be an image.', false);
                                return false;
                            }
                            return true;
                        }
                    });
                }, 100);
            }
        },
        ready : function () {
            console.log('Size component ready');
            this.addSizeContainer();
        },
        methods : {
            getStyleSizes : function(styleId) {
                var style = $.grep(inventoryTypes[0].styles, function(e){
                    return parseInt(e.id) === parseInt(styleId);
                });

                if (style.length > 0 ) {
                    return style[0].sizes;
                }

                return [];
            },
            deleteSizeImage : function(size_container, img) {
                size_container.images.$remove(img);
            },
            createSizeObject : function() {
                var rand = Math.random().toString(36).slice(2);
                var styleObject = this.getStyleSizes($('#inventory-styles-el').val());

                return {id: rand, images: [], sizing: styleObject};
            },
            addSizeContainer : function() {
                var sizeContainerData = this.createSizeObject();

                this.size_containers.push(sizeContainerData);
                this.$emit('size-container:added', sizeContainerData);
            },
            deleteSizeContainer : function(size) {
                this.size_containers.$remove(size);
                this.$emit('size-container:removed', size);
            },
            toggleCategory : function(e) {
                e.preventDefault();
                var $el = $(e.target),
                        $categoryWrapperEl = $el.closest('.box-footer').find('.categories_wrapper');
                if ($categoryWrapperEl.is(':visible')) {
                    $categoryWrapperEl.hide();
                    $el.html('Add Categories');
                } else {
                    $categoryWrapperEl.show();
                    $el.html('Disable Categories');
                }
            }
        }
    });

    new Vue({
        el: '#inventory',
        methods : {
            addSizeContainer : function() {
                this.$broadcast('add-size');
            },
            styleChanged : function(e) {
                this.$broadcast('style-changed', $(e.target).val());
            }
        },
        ready: function(){
            console.log('Inventory ready.');
        }
    });
</script>
@endpush