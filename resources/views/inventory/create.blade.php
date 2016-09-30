@extends('layouts.body_w_leftnav')

@section('body-content-left-nav')
    @include('inventory.partials._leftnav')
@endsection

@section('body-inner-content')
    @include('widgets._fileuploadscripts')

    {{ Form::open(['route' => ['shop.inventory.store', user()->username]]) }}

    <div class="box">
        <div class="box-header">
            <h2>Add Items to your Inventory</h2>
            <small>Once an item is in your inventory, you can add it to any sale, anytime!</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                <label for="categories" class="col-sm-3 form-control-label">Categories</label>
                <div class="col-sm-9">
                    {{ Form::select('categories', \Kabooodle\Models\Categories::all()->sortBy('name')->pluck('name','id'), [], ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                <label for="name" class="col-sm-3 form-control-label">Name</label>
                <div class="col-sm-9">
                    {{ Form::text('name', null, ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                <label for="description" class="col-sm-3 form-control-label">Description</label>
                <div class="col-sm-9">
                    {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}">
                <label for="price_usd" class="col-sm-3 form-control-label">Price in $</label>
                <div class="col-sm-9">
                    {{ Form::number('price_usd', null or 0, ['class' => 'form-control float', 'step' => 'any', 'min' => 0]) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('initial_qty') ? 'has-danger' : null }}">
                <label for="initial_qty" class="col-sm-3 form-control-label">Current Total Quantity</label>
                <div class="col-sm-9">
                    {{ Form::number('initial_qty', null or 0, ['class' => 'form-control', 'v-model' => 'initial_qty', 'number']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('tags') ? 'has-danger' : null }}">
                <label for="tags" class="col-sm-3 form-control-label">Tags or Keywords</label>
                <div class="col-sm-9">
                    {{ Form::text('tags', null, ['class' => 'form-control selectized']) }}
                </div>
            </div>
            <div class="form-group row">
                <label for="tags" class="col-sm-3 form-control-label">Add to Flash Sale
                    <small class="text-muted block">(This can be done later)</small>
                </label>
                <div class="col-sm-9">
                    @if(user()->flashsalesAsSeller->count() > 0)
                    @foreach(user()->flashsalesAsSeller as $flashSale)
                        <div class="form-group">
                            <label class="md-check">
                                <input type="checkbox" class="has-value" name="flashsales[]"
                                       value="{{ $flashSale->id }}">
                                <i class="green"></i>
                                {!! $flashSale->name !!}
                            </label>
                        </div>
                    @endforeach
                    @else
                        <p class="text-muted"><em>You are not currently an admin or seller in any flash sales.</em></p>
                    @endif
                </div>
            </div>

            <div class="form-group row m-t-md">
                <label for="tags" class="col-sm-3 form-control-label">Images</label>
                <div class="col-sm-9">
                    <div class="row clearfix">
                        <div is="inventory-component"></div>
                    </div>

                    <div id="js-program_logo_upload"></div>

                    <div id="uploadTemplate">
                        <button type="button" class="btn white  fileinput-button" style="display: inline-block;">
                            Add Image(s)<input type="file" name="file" class="js-s3_fileupload" accept='image/*' multiple/>
                        </button>
                        <button type="button" class="btn danger js-cancel_button" style="display: none;">
                            Cancel
                            <div class="js-fileupload-progress fileupload-progress m-b-0 p-b-0" style="display: none;  margin-left: -9px; margin-right: -9px;">
                                <div style="height: 8px;" class="progress progress-striped active m-b-0 p-b-0" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="5">
                                    <div class="progress-bar progress-bar-success" style="width: 5%;"></div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
            <a class="m-l text-muted" href="{{ route('shop.inventory.index', [user()->username]) }}">Cancel</a>
        </div>
    </div>

    {{ Form::close() }}


    <script id="thumbnail-template" type="text/x-template">

        <div v-for="image in images">
            <div class="col-sm-6 thumbnail-container">
                <div class="box">
                    <div class="item" >
                        <div class="item-overlay active p-a p-b-0" style="z-index: 99">
                            <button type="button" v-on:click="deleteFile" data-id="@{{ image.id }}" class="pull-right btn btn-xs white text-danger"><i class="fa fa-trash fa-fw"></i></button>
                        </div>
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
                        {{--<div class="m-t-1">--}}
                            {{--<button type="button" class="image_asalbum_btn btn btn-block image_item_component white text-muted text-sm" v-on:click="convertImageToAlbumImage" style="display: none">--}}
                                {{--or Add Image back to album--}}
                            {{--</button>--}}
                            {{--<button type="button" class="image_asitem_btn image_album_component btn btn-block text-muted white text-sm" v-on:click="convertImageToItem" >--}}
                                {{--Convert to new inventory item--}}
                                {{--<input type="hidden" value="1" name="images[@{{ image.key }}][album_item]"   />--}}
                            {{--</button>--}}
                        {{--</div>--}}
                    </div>
                </div>
            </div>
        </div>

    </script>


    <script>
        var inventoryImages = [{!! ( old('images') ? json_encode(old('images')) : null) !!}];
        var InventoryComponent = Vue.extend({
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
//                convertImageToAlbumImage : function(e) {
//                    var scope = this,
//                            $this = $(e.target),
//                            $container = $this.closest('.thumbnail-container');
//
//                    $container.find('.image_item_component').hide().find(':input').prop('disabled', true);
//                    $container.find('.image_album_component').show().find(':input').prop('disabled', false);
//                },
//                convertImageToItem : function(e) {
//                    var scope = this,
//                            $this = $(e.target),
//                            $container = $this.closest('.thumbnail-container');
//
//                    $container.find('.image_album_component').hide().find(':input').prop('disabled', true);
//                    $container.find('.image_item_component').show().find(':input').prop('disabled', false);
//                },
                deleteFile : function(e) {
                    var scope = this,
                            $this = $(e.target);

                    $this.closest('.thumbnail-container').remove();
                }
            }
        });

        Vue.component('inventory-component', InventoryComponent);
    </script>

@endsection

@push('footer-scripts')
<script>

    $(function () {
        $('#js-program_logo_upload').s3uploader({
            save_file_model: false,
            multiple: true,
            s3_bucket: 'kabooodle-storage',
            s3_key_url: '{{ route('api.files.sign') }}',
            s3_key_payload: {
                user: '{{ user()->public_hash }}'
            },
            templateEl: $('#uploadTemplate'),
            fileupload_options: {},
            on_s3_upload: function (data, textStatus, jqXHR) {
                if ($.inArray(jqXHR.status, [201,200]) == -1) {
                    alert('An error occurred with an image, please try that image again.');
                    return false;
                }
                var xml = $(data);
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
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    });
</script>
@endpush