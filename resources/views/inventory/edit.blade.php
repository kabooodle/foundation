@extends('layouts.body_w_leftnav')

@section('body-content-left-nav')
    @include('inventory.partials._leftnav')
@endsection

@section('body-inner-content')

    @push('header-styles')
    <link rel="stylesheet" href="/assets/css/merchant.css?{{ getAppVersion() }}" type="text/css"  />
    @endpush

    @if($item->flashsales && $item->flashsales->count() > 0)
    <div class="box-color p-a warning b-0">
        <h6 class="text-center m-b-0"><strong class="_700">Note:</strong> This item is currently listed in <kbd>{{ $item->flashsales->count() }}</kbd> active flash sales.</h6>
    </div>
    @endif

    @if($item->pendingClaims && $item->pendingClaims->count() > 0)
        <div class="box-color p-a warning b-0">
            <h6 class="text-center m-b-0"><strong class="_700">Note:</strong> This item currently has <kbd>{{ $item->pendingClaims->count() }}</kbd> pending claims.</h6>
        </div>
    @endif

    @if($item->facebooksales && $item->facebooksales->count() > 0)
        <div class="box-color p-a warning b-0">
            <h6 class="text-center m-b-0"><strong class="_700">Note:</strong> This item is currently listed in <kbd>{{ $item->facebooksales->count() }}</kbd> facebook albums.</h6>
        </div>
    @endif

    {{ Form::open(['route' => ['shop.inventory.update', $item->user->username, $item->getUUID()], 'method' => 'put']) }}
    <div class="box">
        <div class="box-header">
            <div class="form-group row {{ $errors->has('name') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Name</label>
                <div class="col-sm-9">
                    {{ Form::text('name', $item->name, ['class' => 'form-control']) }}
                </div>
            </div>
        </div>
        <div class="box-divider m-a-0"></div>
        <div class="box-body">
            <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                <label for="inputPassword3" class="col-sm-3 form-control-label">Description</label>
                <div class="col-sm-9">
                    {{ Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 2]) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('price_usd') ? 'has-danger' : null }}">
                <label for="price_usd" class="col-sm-3 form-control-label">Price in $</label>
                <div class="col-sm-9">
                    {{ Form::number('price_usd', $item->getPrice(), ['class' => 'form-control float', 'step' => 'any', 'min' => 0]) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('initial_qty') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Available Quantity</label>
                <div class="col-sm-9">
                    {{ Form::number('initial_qty', $item->initial_qty, ['class' => 'form-control', 'v-model' => 'initial_qty', 'number']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('initial_qty') ? 'has-danger' : null }}">
                <label for="inputEmail3" class="col-sm-3 form-control-label">Categories</label>
                <div class="col-sm-9">
                    {{ Form::select('categories', \Kabooodle\Models\Categories::all()->sortBy('name')->pluck('name','id'), ($item->categories->count() > 0 ? $item->categories->first()->id : null), ['class' => 'form-control']) }}
                </div>
            </div>
            <div class="form-group row {{ $errors->has('description') ? 'has-danger' : null }}">
                <label for="inputPassword3" class="col-sm-3 form-control-label">Tags or Keywords</label>

                <div class="col-sm-9">
                    {{ Form::text('tags', $item->tagsList(), ['class' => 'form-control selectized', 'id' => 'tags']) }}
                </div>
            </div>


        </div>
    </div>

    @if($item->files->count() > 0)
        <div class="row">
    @foreach($item->files as $file)
        <div class="col-sm-4">
            <img src="{{ $file->location }}" class="w-full"/>
        </div>
    @endforeach
        </div>
    @endif

    <div class="form-group row m-t-md">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn primary">Save</button>
            <a href="{{ route('shop.inventory.show', [$item->user->username, $item->getUUID()]) }}" class="text-muted m-l">Cancel</a>
        </div>
    </div>

    {{ Form::close() }}



@endsection

@push('footer-scripts')
<script>
    $(function(){
        var tags = [
                @foreach ($item->tagsArray() as $tag)
            {tag: "{{$tag}}" },
            @endforeach
        ];

        $('.selectized').selectize({
            delimiter: ',',
            persist: false,
            valueField: 'tag',
            labelField: 'tag',
            searchField: 'tag',
            options : tags,
            plugins: ['remove_button'],
            create: function(input) {
                return {
                    tag: input
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