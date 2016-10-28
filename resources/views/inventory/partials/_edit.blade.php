

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



{{--<validator--}}
        {{--name="inventory_validation"--}}
        {{--:classes="{ invalid : ' has-danger ' }"--}}
{{-->--}}

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
            <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}" >
                <label for="price_usd" class="col-sm-3 form-control-label">Price in USD$</label>
                <div class="col-sm-7">
                    {{ Form::number('price_usd', $item->getPrice(), ['class' => 'form-control float', 'step' => 'any', 'min' => 0,  'placehodler' => '0.00']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('initial_qty') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Available Quantity</label>
                <div class="col-sm-7">
                    {{ Form::number('initial_qty', $item->initial_qty, ['class' => 'form-control','number',]) }}
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


{{--</validator>--}}

{{ Form::close() }}

<script>

</script>
