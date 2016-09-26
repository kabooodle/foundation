@extends('layouts.body_w_leftnav')

@section('body-content-left-nav')
    @include('inventory.partials._leftnav')
@endsection

@push('header-styles')
<link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
@endpush


@push('footer-scripts')
@include('widgets._fileuploadscripts')
<script>
    $(function(){
        $('#js-program_logo_upload').s3uploader({
            button_name: 'Set Logo',
            save_file_model: false,
            multiple: true,
            s3_bucket: 'kabooodle-storage',
            s3_key_url: '//api.kabooodle.dev/files',
            s3_key_payload: {
                user: '{{ user()->public_hash }}'
            },
            fileupload_options: {},
            on_s3_upload: function(data) {

            },
            on_file_add: function (element, data) {
//                if($.inArray(data.files[0].type, ['image/png', 'image/jpeg']) == -1) {
//                    alert('Image must be png or jpg.', false);
//                    return false;
//                }
                return true;
            }
        });
        $('.selectized').selectize({
            delimiter: ',',
            persist: false,
            create: function(input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
    });
</script>
@endpush


@section('body-inner-content')


    {{ Form::open(['route' => ['shop.inventory.store', user()->username]]) }}
    <div id="js-program_logo_upload">

    </div>
    <div class="box">
        <div class="box-header">
            <h2>Add Items to your Inventory</h2>
            <small>Once an item is in your inventory, you can add it to any sale, anytime!</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">

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
            <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                <label for="categories" class="col-sm-3 form-control-label">Categories</label>
                <div class="col-sm-9">
                    {{ Form::select('categories', \Kabooodle\Models\Categories::all()->sortBy('name')->pluck('name','id'), [], ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('tags') ? 'has-danger' : null }}">
                <label for="tags" class="col-sm-3 form-control-label">Tags or Keywords</label>
                <div class="col-sm-9">
                    {{ Form::text('tags', null, ['class' => 'form-control selectized']) }}
                </div>
            </div>
            <div class="form-group row">
                <label for="tags" class="col-sm-3 form-control-label">Add to Flash Sale<small class="text-muted block">(This can be done later)</small></label>
                <div class="col-sm-9">
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

@endsection