@extends('layouts.body_w_leftnav', ['contentId' =>'inventory_manage'])

@section('body-content-left-nav')
    <div class="box no-shadow m-b-0">
        <div class="box-body" id="images_el" >
            <div v-if="images.length">
                <div class="item" v-for="(index,image) in images">
                    <div class="item-overlay active p-l p-r " style="z-index: 999;">
                        <a type="button" style="" v-on:click="deleteImage(image, $event)" data-id="@{{ image.id }}" class="pull-right text-danger"><i class="fa fa-times fa-fw"></i></a>
                        <span class="pull-left label dark-white text-color">@{{ images.length - $index }}</span>
                    </div>
                    <div class="thumbnail" >
                        <img
                                :src="image.location"
                                data-gallery="gallery_@{{ item.id }}"
                                class="w-full"
                                data-toggle="lightbox"
                                data-remote="@{{ image.location }}"/>
                    </div>
                </div>
            </div>
            <div v-else>
                No Images
            </div>
        </div>
    </div>
@endsection

@section('body-inner-content')
    @include('widgets._fileuploadscripts')

    @if($item->flashsales->count() > 0 || $item->facebooksales->count() > 0 || $item->pendingClaims->count() > 0)
        <div class="box-color p-a white b-0">
            @if($item->flashsales && $item->flashsales->count() > 0)
                <h6 class="text-center m-b-0">This item is currently listed in <kbd>{{ $item->flashsales->count() }}</kbd>
                    active flash {{ str_plural('sale', $item->flashsales->count()) }}.</h6>
            @endif
            @if($item->facebooksales && $item->facebooksales->count() > 0)
                <h6 class="text-center m-b-0">This item is currently listed in
                    <kbd>{{ $item->facebooksales->count() }}</kbd> facebook albums.</h6>
            @endif
            @if($item->pendingClaims && $item->pendingClaims->count() > 0)
                <h6 class="text-center m-b-0">This item currently has <kbd>{{ $item->pendingClaims->count() }}</kbd>
                    pending {{ str_plural('claim', $item->pendingClaims->count()) }}.</h6>
            @endif
        </div>
    @endif



        {{ Form::open(['id' => 'form_inventory_manage', 'v-on:submit' => 'validateForm', 'route' => ['shop.inventory.update', $item->user->username, $item->getUUID()], 'method' => 'put']) }}

        <input v-for="image in images" type="hidden" name="images[@{{ image.id }}][data]"  value="@{{ image.json }}">

        <validator
                name="inventory_validation"
                :classes="{ invalid : ' has-danger ' }"
        >

        <div class="box">
            <div class="box-body">

                <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Style</label>
                    <div class="col-sm-7">
                        {{ Form::select('style_id', $styles->pluck('name', 'id'), $item->style->id, ['class' => 'form-control', 'v-on:change' => 'changeStyle']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('size') ? 'has-danger' : null }}">
                    <label for="inputPassword3" class="col-sm-3 form-control-label">Size</label>
                    <div class="col-sm-7">
                        {{ Form::select('size_id', $item->style->sizes->pluck('name','id'), $item->styleSize->id, ['class' => 'form-control', 'id' => 'form_size_el']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}" v-validate-class>
                    <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
                    <div class="col-sm-7">
                        {{ Form::number('price_usd', $item->getPrice(), ['class' => 'form-control float', 'step' => 'any', 'min' => 0, 'v-validate:price' => '{ required : true }', 'v-model' => 'price', 'placehodler' => '0.00']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('initial_qty') ? 'has-danger' : null }}" v-validate-class>
                    <label for="inputEmail3" class="col-sm-3 form-control-label">Available Quantity</label>
                    <div class="col-sm-7">
                        {{ Form::number('initial_qty', $item->initial_qty, ['class' => 'form-control', 'v-model' => 'initial_qty', 'number', 'v-validate:initial_qty' => '{ required : true }',]) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                    <label for="inputPassword3" class="col-sm-3 form-control-label">Description<small class="block text-muted text-sm">(Optional)</small></label>
                    <div class="col-sm-7">
                        {{ Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 2]) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                    <label for="inputPassword3" class="col-sm-3 form-control-label">Categories<small class="block text-muted text-sm">(Optional)</small></label>

                    <div class="col-sm-7">
                        {{ Form::text('categories', $item->tagsString(), ['class' => 'selectized', 'id' => 'tags', 'placeholder' => 'Type categories']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <span class="pull-left">
                    <image-attach btn-class-size="" multiple="false"></image-attach>
                </span>
                <button type="submit" class="btn primary">Save</button>
            </div>
        </div>
        </validator>

        {{ Form::close() }}



@endsection

@push('footer-scripts')
<script>

    new Vue({
        el: '#inventory_manage',
        data: {
            styles: {!! $styles->toJson() !!},
            style: {!! $item->style->toJson() !!},
            sizes: {!! $item->style->sizes->toJson() !!},
            item: {!!  $item->toJson()  !!},
            images: {!! $item->files->toJson() !!}
        },
        ready: function () {
            console.log('Inventory management ready');
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
        },
        methods: {
            getStyleById: function(id) {
                return $.findFirst(this.styles, function(obj) {
                    return obj.id == id;
                });
            },
            setSizes : function(sizes){
                this.sizes = sizes;
            },
            setStyle : function(style){
                this.style = style;
            },
            changeStyle: function(e){
                var $el = $(e.target),
                        id = parseInt($el.val()),
                        style = this.getStyleById(id);

                this.setStyle(style);
                this.setSizes(style.sizes);
            },
            validateForm: function (e) {
                var scope = this,
                        $that = $(e.target);

                this.$validate(true, function () {
                    if (scope.$inventory_validation.invalid || scope.images.length == 0) {
                        e.preventDefault();
                        if (scope.images.length == 0) {
                            alert('Must have at least 1 image');
                        }
                        return false;
                    }
                });
            },
            deleteImage: function(image){
                this.images.$remove(image);
            },
            insertImage: function(image) {
                this.images.unshift(image);
            }
        },
        events: {
            'image:uploaded' : function(el, image) {
                this.insertImage(image);
            }
        },
        watch : {
            sizes : function(v){
                var formSizeEl = $('#form_size_el');
                formSizeEl.empty();
                $.each(v, function(i,value){
                    formSizeEl.append($('<option>').text(value.name).prop('value', value.id));
                });
            }
        }
    });
</script>
@endpush