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
                        {{ Form::select('styles', $inventoryTypes->first()->styles->pluck('name','id'), [], ['id' => 'inventory-styles-el', 'class' => ' form-control ']) }}
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

                {{--<div class="modal fade" id="modals">--}}
                {{--<div class="modal-dialog" role="document">--}}
                {{--<div class="modal-content">--}}
                {{--<div class="modal-body">--}}
                {{--<div class="clearfix text-center center-block">--}}
                {{--<div class="btn-group btn-group-md col-sm-7" role="group" aria-label="Basic example">--}}
                {{--<button type="button" class="btn white">Small</button>--}}
                {{--<button type="button" class="btn white">Large</button>--}}
                {{--<button type="button" class="btn white">XL</button>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="form-group row m-t-md">--}}
                {{--<label for="tags" class="col-sm-3 form-control-label">Images</label>--}}
                {{--<div class="col-sm-7">--}}
                {{--<div class="row clearfix">--}}
                {{--<div is="inventory-component"></div>--}}
                {{--</div>--}}

                {{--<div id="js-program_logo_upload"></div>--}}

                {{--<div id="uploadTemplate">--}}
                {{--<button type="button" class="btn white  fileinput-button" style="display: inline-block;">--}}
                {{--Add Image(s)<input type="file" name="file" class="js-s3_fileupload" accept='image/*' multiple/>--}}
                {{--</button>--}}
                {{--<button type="button" class="btn danger js-cancel_button" style="display: none;">--}}
                {{--Cancel--}}
                {{--<div class="js-fileupload-progress fileupload-progress m-b-0 p-b-0" style="display: none;  margin-left: -9px; margin-right: -9px;">--}}
                {{--<div style="height: 8px;" class="progress progress-striped active m-b-0 p-b-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="5">--}}
                {{--<div class="progress-bar progress-bar-success" style="width: 5%;"></div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</button>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--</div><!-- /.modal-content -->--}}
                {{--</div><!-- /.modal-dialog -->--}}
                {{--</div><!-- /.modal -->--}}




            </div>
        </div>

        <div is="inventory-sizing"></div>




        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <button type="submit" class="btn primary">Save</button>
                <button type="button" class="btn white" id="size-add-btn" v-on:click="addSizeContainer">Add Size</button>
            </div>
        </div>

        {{ Form::close() }}


        <script id="thumbnail-template" type="text/x-template">
            <div v-for="image in images">
                <div class="col-sm-4 thumbnail-container">
                    <div class="box">
                        <div class="item" >
                            <button type="button" style="z-index: 999; position: absolute; top: 0; right: 0; border: 0; border-radius: 0;" v-on:click="deleteFile(image, $event)" data-id="@{{ image.id }}" class="pull-right btn btn-xs white text-danger"><i class="fa fa-trash fa-fw"></i></button>
                            <div class="thumbnail">
                                <img :src="image.location" data-gallery="image-thumbs" data-width="100%" class="w-full"  data-toggle="lightbox" data-remote="@{{ image.location }}"/>
                            </div>
                        </div>
                        <input type="hidden" name="images[@{{ image.key }}][data]" value="@{{ image | json }}" />
                        <div class="box-body m-t-0 p-t-0">
                            <div class="image_item_component">
                                <label class="text-muted text-sm m-b-0 p-b-0">Quantity:</label>
                                <input type="text" value="1" name="images[@{{ image.key }}][qty]" class="text-center image_qty_btn"  />
                                <input type="hidden" value="1" name="images[@{{ image.key }}][item_new]"  />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </script>


        <script id="sizing-template" type="text/x-template">
            <div v-for="size in sizes">
                <div class="box">
                    <div class="box-body clearfix">
                        <div class="form-group row">
                            <label class="col-sm-3 form-control-label">Size</label>
                            <div class="col-sm-7">
                                <div class="btn-group-justified" data-toggle="buttons">
                                    <label class="btn white">
                                        <input type="radio" name="options" id="option1" autocomplete="off" checked> XS
                                    </label>
                                    <label class="btn white">
                                        <input type="radio" name="options" id="option2" autocomplete="off"> S
                                    </label>
                                    <label class="btn  white">
                                        <input type="radio" name="options" id="option3" autocomplete="off"> M
                                    </label>
                                </div>
                            </div>
                            <button title="Delete" data-toggle="tooltip" data-position="left" type="button" v-on:click="deleteSizeContainer(size)" style="position: absolute; top: 0; right: 0; border: 0; border-radius: 0;" class="m-l-1 pull-right btn white btn-xs text-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                        </div>

                        <div class="row clearfix">
                            <div is="inventory-component"></div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <div class="form-group categories_wrapper" style="display: none">
                            <input type="text" name="" class="form-control selectized" placeholder="Type categories or keywords">
                        </div>
                        <div class="clearfix">
                            <div class="js-program_logo_upload"></div>
                            <div class="pull-left upload-template">
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

        <script>
            var inventoryImages = [{!! ( old('images') ? json_encode(old('images')) : null) !!}];
            Vue.component('inventory-component', {
                template: '#thumbnail-template',
                data: function () {
                    return {
                        images: inventoryImages
                    }
                },
                ready : function(){
                    this.imageInserted();
                },
                watch : {
                    'images' : {
                        handler: function (value,mutation) {
                            this.imageInserted();
                        }
                    }
                },
                methods : {
                    imageInserted: function(){
                        $('.thumbnail-container').find("input.image_qty_btn").TouchSpin({
                            min: 1
                        });
                    },
                    deleteFile : function(image, e) {
                        var scope = this,
                                $this = $(e.target);

                        $this.closest('.thumbnail-container').remove();
                        scope.images.$remove(image);
                    }
                }
            });

            Vue.component('inventory-sizing', {
                template: '#sizing-template',
                data: function () {
                    return {
                        sizes : [{id : Math.random() }]
                    }
                },
                events : {
                    'add-size' : function(size) {
                        this.sizes.push(size);
                    },
                    'image-added' : function(image) {

                    }
                },
                methods : {
                    deleteSizeContainer : function(size) {
                        this.sizes.$remove(size);
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
                        this.$broadcast('add-size', {id: Math.random()});
                    }
                },
                ready: function(){
                    var $this = this;
                    $(function () {
                        $('.js-program_logo_upload').s3uploader({
                            save_file_model: false,
                            multiple: true,
                            s3_bucket: 'kabooodle-storage',
                            s3_key_url: '{{ route('api.files.sign') }}',
                            s3_key_payload: {
                                user: '{{ user()->public_hash }}'
                            },
                            templateEl: $('.upload-template'),
                            fileupload_options: {},
                            on_s3_upload: function (data, textStatus, jqXHR) {
                                if ($.inArray(jqXHR.status, [201,200]) == -1) {
                                    alert('An error occurred with an image, please try that image again.');
                                    return false;
                                }
                                var xml = $(data);

                                $this.$broadcast('image-added', {
                                    id:         xml.find('Key').text(),
                                    bucket:     xml.find('Bucket').text(),
                                    key:        xml.find('Key').text(),
                                    location:   xml.find('Location').text()
                                });

                                inventoryImages.push({
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

                        $('.selectized').selectize({
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

                    console.log('Inventory ready.');
                }
            });
        </script>

    </div>
@endsection

@push('footer-scripts')
<script>


</script>
@endpush