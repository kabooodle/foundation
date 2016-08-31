@extends('layouts.body_w_leftnav')



@section('body-content-left-nav')
    @include('inventory.partials._leftnav')
@endsection

@section('body-inner-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    <div class="box">
        <div class="box-header">
            <h2>Add Items to your Inventory</h2>
            <small>Once an item is in your inventory, you can add it to any sale, anytime!</small>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            {{ Form::open(['route' => ['shop.inventory.store', user()->username]]) }}
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
            <div class="form-group row {{ $errors->has('current_qty') ? 'has-danger' : null }}">
                <label for="current_qty" class="col-sm-3 form-control-label">Current Total Quantity</label>
                <div class="col-sm-9">
                    {{ Form::number('current_qty', null or 0, ['class' => 'form-control', 'v-model' => 'current_qty', 'number']) }}
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
            <div class="form-group row m-t-md">
                <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" class="btn primary">Save</button>
                    <a class="m-l text-muted" href="{{ route('shop.inventory.index', [user()->username]) }}">Cancel</a>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    @push('footer-scripts')
    <script>
        $(function(){
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

            $('.float').keypress(function(event) {
                if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            });

        });
    </script>

    @endpush

@endsection