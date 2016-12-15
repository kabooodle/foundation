@extends('layouts.full', ['contentId' => 'inventory'])

@push('header-scripts')
    <script>
        KABOOODLE_APP.inventory_types = JSON.parse('{!! $inventoryTypes->toJson()   !!}');
    </script>
@endpush

@section('body-content')
    @include('widgets._fileuploadscripts')



        {{--<validator--}}
            {{--name="inventory_validation"--}}
            {{--:classes="{ invalid : ' has-danger ' }"--}}
        {{-->--}}

        {{ Form::open(['route' => ['shop.inventory.store', user()->username], 'v-on:submit' => 'validateForm']) }}

        <div class="box">
            <div class="box-header">
                <h2>Add Items to your Inventory</h2>
                <small>Once an item is in your inventory, you can add it to any sale, anytime!</small>
            </div>
            <div class="box-divider m-a-0"></div>
            <div class="box-body">

                <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                    <label for="type" class="col-sm-3 form-control-label">Type</label>
                    <div class="col-sm-5">
                        {{ Form::select('type_id', $inventoryTypes->pluck('name','id'), [], ['id' => 'inventory-type-el', 'class' => 'form-control']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('categories') ? 'has-danger' : null }}">
                    <label for="type" class="col-sm-3 form-control-label">Style</label>
                    <div class="col-sm-5">
                        {{ Form::select('style_id', $inventoryTypes->first()->styles->pluck('name','id'), [], ['v-on:change' => 'styleChanged', 'id' => 'inventory-styles-el', 'class' => ' form-control ']) }}
                    </div>
                </div>
                <div class="form-group row {{ $errors->has('wholesale_price_usd') ? 'has-danger' : null }}"  >
                    <label for="wholesale_price_usd" class="col-sm-3 form-control-label">Wholesale Price in USD$</label>
                    <div class="col-sm-3">
                        {{ Form::number('wholesale_price_usd', 0.00, ['class' => 'form-control float', 'required', 'step' => 'any', 'placeholder' => '0.00', 'id' => 'inventory-wholesale-el']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}"  >
                    <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
                    <div class="col-sm-3">
                        {{ Form::number('price_usd', 0.00, ['id' => 'inventory-price-el', 'class' => 'form-control float', 'required', 'step' => 'any', 'placeholder' => '0.00']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                    <label for="description" class="col-sm-3 form-control-label">Description<small class="text-muted block text-sm">(Optional)</small></label>
                    <div class="col-sm-7">
                        {{ Form::textarea('description', null, ['class' => 'form-control', 'rows' => 2]) }}
                    </div>
                </div>
            </div>
        </div>

        <inventory-sizing
                user_hash="{{ user()->public_hash }}"
                s3_key_url="{{ route('api.files.sign') }}"
                :size_containers.sync="size_containers"></inventory-sizing>

        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <button style="margin-left: 6px;" type="button" class="btn white" id="size-add-btn" v-on:click="addSizeContainer" :disabled="submitting">Add Size</button>
                <button type="submit" class="btn primary" id="btn-form-save">Save</button>
            </div>
        </div>

        {{ Form::close() }}

        {{--</validator>--}}

@endsection

@push('footer-scripts')

<script src="{{staticAsset('/assets/js/inventory-create.js')}}"></script>
@endpush