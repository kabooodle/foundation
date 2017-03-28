@extends('layouts.full', ['contentId' => 'inventory'])

@push('header-scripts')
    <script>
        KABOOODLE_APP.inventory_types = JSON.parse('{!! $inventoryTypes->toJson()   !!}');
    </script>
@endpush

@section('body-content')
    @include('widgets._fileuploadscripts')

        {{ Form::open(['route' => ['shop.inventory.store', webUser()->username], 'v-on:submit' => 'validateForm']) }}

        <div class="box" id="addItemsPrimaryContainer">
            <div class="box-header">
                <h2>Add Items to your Inventory
                    <a class="text-success onboard-show-btn" @click.prevent="showTourModal">
                        <i class="fa fa-question-circle" aria-hidden="true"></i>
                    </a>
                </h2>
                <small>Once an item is in your inventory, you can add it to any sale or outfit, anytime!</small>
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
                        {{ Form::number('wholesale_price_usd', null, ['class' => 'form-control float', 'required', 'step' => 'any',  'v-model'=>'wholesale_price', 'placeholder' => '0.00', 'id' => 'inventory-wholesale-el']) }}
                    </div>
                </div>

                <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}"  >
                    <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
                    <div class="col-sm-3">
                        {{ Form::number('price_usd', null, ['id' => 'inventory-price-el', 'v-model' => 'price', 'class' => 'form-control float', 'required', 'step' => 'any', 'placeholder' => '0.00']) }}
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

        <size-containers
                s3_bucket="{{ env('AWS_BUCKET') }}"
                s3_acl="public-read"
                s3_key_url="{{ apiRoute('api.files.sign') }}"
        ></size-containers>


        <div class="form-group row m-t-md">
            <div class="col-sm-offset-3 col-sm-7">
                <button style="margin-left: 6px;" type="button" class="btn white" id="size-add-btn" @click="addSizeContainer" :disabled="submitting">Add Size</button>
                <button type="submit" class="btn primary" id="btn-form-save">Save</button>
            </div>
        </div>

        {{ Form::close() }}


    <modal modal_id="tour-modal" modal_class="tour-modal" :display_header="false" :display_footer="false">
        <div slot="modal_body">
            <div class="m-a-2 p-a-2 center text-center block-center center-block">
                <img src="/assets/images/kit1.png">
                <h5 class="m-t-3 m-b-3">I'm Kit. If you need help, I'm here to help guide you anytime.</h5>
                <button type="button" class="btn btn-success btn-lg" @click.prevent="startTour">Yes, take a tour</button>
                <button type="button" class="m-l-1 btn btn-lg white" @click.prevent="noTour">No thanks Kit!</button>
            </div>
        </div>
    </modal>

@endsection

@push('footer-scripts')

<script src="{{staticAsset('/assets/js/inventory-create.js')}}"></script>
@endpush