<div id="inventory_manage">
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


{{ Form::open([
    'id' => 'form_inventory_manage',
    'v-on:submit' => 'validateForm',
    'route' => ['shop.inventory.update', $item->user->username, $item->getUUID()],
    'method' => 'put'
]) }}


{{--<validator--}}
{{--name="inventory_validation"--}}
{{--:classes="{ invalid : ' has-danger ' }"--}}
{{-->--}}


<inventory-edit
        :styles="{{ $styles->toJson() }}"
        :item="{{ $item->toJson() }}"
        :existingimages="{{ $item->files->toJson() }}"
        tags="{!! $item->tagsString()  !!}"
        api_route="{{ route('api.files.sign') }}"
></inventory-edit>

<script src="/assets/js/inventory-edit.js"></script>
{{--</validator>--}}

{{ Form::close() }}
</div>